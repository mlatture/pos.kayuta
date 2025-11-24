<?php

namespace App\Services;

use App\Models\ContentIdea;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class ContentExpansionService
{
    public function expandFromIdea(ContentIdea $idea): array
    {
        $tenantId = $idea->tenant_id;

        // TODO: future me yahan AI call ayega.
        // Abhi ke liye dummy internal/external content banate hain.

        $slug = Str::slug($idea->title . '-' . $idea->id);

        // Internal voice: "we added a new pool"
        $internalBody = $this->buildInternalMarkdown($idea);

        // External voice: "Kayuta Lake Campground has added a new pool"
        $externalBody = $this->buildExternalMarkdown($idea);

        // Main article representation for internal Page
        $internal = [
            'type'     => 'article', // ya 'blog' agar config se change karna ho
            'title'    => $idea->title,
            'slug'     => $slug,
            'body_md'  => $internalBody,
        ];

        // External/syndicated-style content for Make.com
        $external = [
            'channel' => 'rvcamping',
            'title'   => $idea->title . ' at ' . ($idea->tenant->name ?? 'our campground'),
            'body_md' => $externalBody,
            'meta'    => [
                'idea_id'   => $idea->id,
                'tenant_id' => $idea->tenant_id,
                // future: park url, logo, etc.
            ],
        ];

        // Build social variants
        $variants = $this->buildSocialVariants($idea, $internal);

        // Media list (hero, etc.)
        $media = [
            [
                'type' => 'image',
                'url'  => 'https://picsum.photos/1200/630?random=' . $idea->id,
            ],
        ];

        return [
            'internal' => $internal,
            'external' => $external,
            'variants' => $variants,
            'media'    => $media,
        ];
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
            'tenant_id'   => $idea->tenant_id,
            'idea_id'     => $idea->id,
            'article_url' => $this->buildArticleUrl($idea, $page->slug),
            'variants'    => $result['variants'],
            'media'       => $result['media'],
        ];

        // Future: real call to rvparkhq.com API
        /*
        Http::withToken(config('services.rvparkhq.token'))
            ->post(config('services.rvparkhq.endpoint') . '/api/posts.schedule', $payload);
        */

        logger()->info('HQ payload (stub)', $payload);
    }
}
