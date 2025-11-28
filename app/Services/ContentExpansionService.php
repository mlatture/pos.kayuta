<?php

namespace App\Services;

use App\Models\ContentIdea;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class ContentExpansionService
{
    
     protected AIContentService $ai;

    public function __construct(AIContentService $ai)
    {
        $this->ai = $ai;
    }


   
   
    public function expandFromIdea(ContentIdea $idea): array
    {
        $slug = Str::slug($idea->title . '-' . $idea->id);

        // 1) Pehle AI se try karo
        $aiResult = $this->ai->expandIdea($idea);

        // 2) Internal
        if (!empty($aiResult['internal'])) {
            $internal = [
                'type'    => $aiResult['internal']['type']    ?? 'article',
                'title'   => $aiResult['internal']['title']   ?? $idea->title,
                'slug'    => $aiResult['internal']['slug']    ?? $slug,
                'body_md' => $aiResult['internal']['body_md'] ?? $this->buildInternalMarkdown($idea),
            ];
        } else {
            // fallback: tumhara purana static logic
            $internal = [
                'type'    => 'article',
                'title'   => $idea->title,
                'slug'    => $slug,
                'body_md' => $this->buildInternalMarkdown($idea),
            ];
        }

        // 3) External
        $parkName = $idea->tenant->name ?? 'our campground';

        if (!empty($aiResult['external'])) {
            $external = [
                'channel' => $aiResult['external']['channel'] ?? 'rvcamping',
                'title'   => $aiResult['external']['title']   ?? $idea->title . ' at ' . $parkName,
                'body_md' => $aiResult['external']['body_md'] ?? $this->buildExternalMarkdown($idea),
                'meta'    => $aiResult['external']['meta']    ?? [
                    'idea_id'   => $idea->id,
                    'tenant_id' => $idea->tenant_id,
                ],
            ];
        } else {
            $external = [
                'channel' => 'rvcamping',
                'title'   => $idea->title . ' at ' . $parkName,
                'body_md' => $this->buildExternalMarkdown($idea),
                'meta'    => [
                    'idea_id'   => $idea->id,
                    'tenant_id' => $idea->tenant_id,
                ],
            ];
        }

        // 4) Variants
        if (!empty($aiResult['variants'])) {
            $variants = $this->normalizeAndAttachUtmToVariants($idea, $internal, $aiResult['variants']);
        } else {
            $variants = $this->buildSocialVariants($idea, $internal);
        }

        // 5) Media
        if (!empty($aiResult['media'])) {
            // agar AI sirf prompts diya hai, tum yahan decide karo ke kaun sa actual image url use karna
            $media = [];
            foreach ($aiResult['media'] as $item) {
                $media[] = [
                    'type' => 'image',
                    'url'  => 'https://picsum.photos/1200/630?random=' . $idea->id,
                    'prompt' => $item['prompt'] ?? null,
                ];
            }
        } else {
            $media = [
                [
                    'type' => 'image',
                    'url'  => 'https://picsum.photos/1200/630?random=' . $idea->id,
                ],
            ];
        }

        return [
            'internal' => $internal,
            'external' => $external,
            'variants' => $variants,
            'media'    => $media,
        ];
    }

    // 👇 yeh naya helper hai
    protected function normalizeAndAttachUtmToVariants(ContentIdea $idea, array $internal, array $variants): array
    {
        $baseUrl = $this->buildArticleUrl($idea, $internal['slug']);

        $normalized = [];

        foreach ($variants as $variant) {
            $platform = strtolower($variant['platform'] ?? 'other');
            $caption  = $variant['caption'] ?? '';

            $query = http_build_query([
                'utm_source'   => $platform,
                'utm_medium'   => 'social',
                'utm_campaign' => 'idea_' . $idea->id,
            ]);

            $utmLink = $baseUrl . '?' . $query;

            // length limits
            if ($platform === 'instagram' && strlen($caption) > 2200) {
                $caption = substr($caption, 0, 2190) . '…';
            }

            if (($platform === 'x' || $platform === 'twitter') && strlen($caption) > 280) {
                $caption = substr($caption, 0, 270) . '…';
            }

            $normalized[] = [
                'platform'  => $platform,
                'caption'   => $caption,
                'media_url' => 'https://picsum.photos/1200/630?random=' . $idea->id, // ya real hero image
                'utm_link'  => $utmLink,
            ];
        }

        return $normalized;
    }
    
    protected function buildInternalMarkdown(ContentIdea $idea): string
{
    $summary = $idea->summary ?: '';

    return <<<MD
# {$idea->title}

We’re excited to share an update from our park.

{$summary}

This post was generated from one of our weekly content ideas and is meant for guests reading our main website.
MD;
}

protected function buildExternalMarkdown(ContentIdea $idea): string
{
    $parkName = $idea->tenant->name ?? 'this campground';
    $summary = $idea->summary ?: '';

    return <<<MD
# {$idea->title} at {$parkName}

{$parkName} has announced a new update for its guests.

{$summary}

This article is written in a third-person voice and is intended for external sites or partner networks that talk about the park from the outside.
MD;
}


    protected function buildArticleUrl($idea, string $slug): string
    {
        // Later: pick from tenant settings (book.<park>.com)
        return 'https://book.kayuta.com/articles/' . $slug;
    }

    protected function buildSocialVariants(ContentIdea $idea, array $internal): array
    {
        $baseUrl = $this->buildArticleUrl($idea, $internal['slug']);

        $utm = function (string $platform) use ($idea, $baseUrl) {
            $query = http_build_query([
                'utm_source'   => $platform,
                'utm_medium'   => 'social',
                'utm_campaign' => 'idea_' . $idea->id,
            ]);

            return $baseUrl . '?' . $query;
        };

        $heroImageUrl = 'https://picsum.photos/1200/630?random=' . $idea->id;

        return [
            [
                'platform'  => 'facebook',
                'caption'   => $idea->title . " 🌲🔥\n\nRead more: " . $utm('facebook'),
                'media_url' => $heroImageUrl,
            ],
            [
                'platform'  => 'instagram',
                'caption'   => $idea->title . " 😍🔥 #camping #familytime\n\nMore: " . $utm('instagram'),
                'media_url' => $heroImageUrl,
            ],
            [
                'platform'  => 'x',
                'caption'   => substr($idea->title . ' - ' . $utm('x'), 0, 270),
                'media_url' => $heroImageUrl,
            ],
        ];
    }

    public function sendToHQScheduler(ContentIdea $idea, $page, array $result): void
    {
        $payload = [
            'tenant_id'    => $idea->tenant_id,
            'tenant_name'  => $idea->tenant->name ?? null,
            'tenant_domain'=> $idea->tenant->primary_domain ?? null,
            'idea_id'      => $idea->id,
            'article_url'  => $this->buildArticleUrl($idea, $page->slug),
            'variants'     => $result['variants'],
            'media'        => $result['media'],
        ];

        $endpoint = rtrim(config('services.rvparkhq.endpoint'), '/') . '/api/posts.schedule';

        try {
            $response = Http::withToken(config('services.rvparkhq.token'))
                ->post($endpoint, $payload);

            logger()->info('HQ payload sent', [
                'payload'  => $payload,
                'status'   => $response->status(),
                'response' => $response->json(),
            ]);
        } catch (\Throwable $e) {
            logger()->error('HQ payload send failed', [
                'payload' => $payload,
                'error'   => $e->getMessage(),
            ]);
        }
    }

}
