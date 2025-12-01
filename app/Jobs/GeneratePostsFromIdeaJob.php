<?php

namespace App\Jobs;

use App\Models\ContentIdea;
use App\Models\Page;
use App\Models\SyndicatedContent;
use App\Models\Article;              // ⬅️ NEW
use App\Services\ContentExpansionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;          // ⬅️ NEW

class GeneratePostsFromIdeaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $ideaId;

    public function __construct(int $ideaId)
    {
        $this->ideaId = $ideaId;
    }

    public function handle(ContentExpansionService $expander): void
    {
        $idea = ContentIdea::with('category', 'tenant')->find($this->ideaId);

        if (! $idea || $idea->status !== 'approved') {
            return;
        }

        // 1) Let the service build internal + external content + social variants
        $result = $expander->expandFromIdea($idea);

        // Normalize internal to avoid undefined index
        $internal = $result['internal'] ?? [
            'type'    => 'article',
            'title'   => $idea->title,
            'slug'    => Str::slug($idea->title . '-' . $idea->id),
            'body_md' => '',
        ];

        // Try to get a hero image from media (first image type)
        $heroImageUrl = null;
        if (!empty($result['media']) && is_array($result['media'])) {
            foreach ($result['media'] as $mediaItem) {
                if (($mediaItem['type'] ?? null) === 'image' && !empty($mediaItem['url'])) {
                    $heroImageUrl = $mediaItem['url'];
                    break;
                }
            }
        }

        // 2) Create internal article/page for book.kayuta.com (existing CMS)
        $page = Page::create([
            'tenant_id'    => $idea->tenant_id,
            'idea_id'      => $idea->id,
            'type'         => $internal['type'] ?? 'article',
            'title'        => $internal['title'],
            'metatitle'    => $internal['title'],
            'slug'         => $internal['slug'],
            'body_md'      => $internal['body_md'],
            'status'       => 1,
            'published_at' => now(),
        ]);

        // 2b) ALSO create an Article record (articles module)
        // Description = short version, or full markdown if you prefer
        $plainBody   = strip_tags($internal['body_md']);
        $description = Str::limit($plainBody, 400); // adjust length as needed

        $article = Article::create([
            'title'               => $internal['title'],           // slug mutator will auto-generate
            'slug'                =>  $internal['slug'],
            'description'         => $description,
            'thumbnail'           => $heroImageUrl ?? null,
            'status'              => 1,
            'metatitle'           => $internal['title'],
            'metadescription'     => Str::limit($plainBody, 155),
            'canonicalurl'        => 'https://book.kayuta.com/article/' . ($internal['slug'] ?? ''),
            'opengraphtitle'      => $internal['title'],
            'opengraphdescription'=> Str::limit($plainBody, 155),
            'opengraphimage'      => $heroImageUrl ?? null,
        ]);

        // 3) Store external/syndicated-style content for Make.com
        $external = $result['external'] ?? null;

        if ($external) {
            SyndicatedContent::create([
                'tenant_id' => $idea->tenant_id,
                'idea_id'   => $idea->id,
                'channel'   => $external['channel'] ?? 'rvcamping',
                'title'     => $external['title'] ?? $idea->title,
                'body_md'   => $external['body_md'] ?? '',
                'meta'      => $external['meta'] ?? [],
                'status'    => 'pending',
            ]);
        }

        // 4) Send social variants payload to RVParkHQ
        $expander->sendToHQScheduler($idea, $page, $result);
    }
}
