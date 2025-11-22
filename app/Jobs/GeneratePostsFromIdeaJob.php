<?php

namespace App\Jobs;

use App\Models\ContentIdea;
use App\Models\Page;
use App\Models\SyndicatedContent;
use App\Services\ContentExpansionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $idea = ContentIdea::with('category')->find($this->ideaId);

        if (! $idea || $idea->status !== 'approved') {
            return;
        }

        // 1) Let the service build internal + external content + social variants
        $result = $expander->expandFromIdea($idea);

        // $result structure:
        // [
        //   'internal'   => ['title','slug','body_md','type'],
        //   'external'   => ['title','body_md','channel','meta'],
        //   'variants'   => [...],
        //   'media'      => [...],
        // ]

        // 2) Create internal article/page for book.kayuta.com
        $page = Page::create([
            'tenant_id'    => $idea->tenant_id,
            'type'         => $result['internal']['type'] ?? 'article',
            'title'        => $result['internal']['title'],
            'slug'         => $result['internal']['slug'],
            'body_md'      => $result['internal']['body_md'],
            'status'       => 'published',
            'published_at' => now(),
        ]);

        // 3) Store external/syndicated-style content for Make.com
        SyndicatedContent::create([
            'tenant_id' => $idea->tenant_id,
            'idea_id'   => $idea->id,
            'channel'   => $result['external']['channel'] ?? 'rvcamping',
            'title'     => $result['external']['title'],
            'body_md'   => $result['external']['body_md'],
            'meta'      => $result['external']['meta'] ?? [],
            'status'    => 'pending',
        ]);

        // 4) Send social variants payload to RVParkHQ (stub for now)
        $expander->sendToHQScheduler($idea, $page, $result);
    }
}
