<?php

namespace App\Services;

use App\Models\ContentIdea;
use OpenAI;
use Illuminate\Support\Str;

class AIContentService
{
    public function expandIdea(ContentIdea $idea): array
    {
        $client      = OpenAI::client(config('services.openai.key'));
        $parkName    = $idea->tenant->name ?? 'our campground';
        $parkUrl     = $idea->tenant->primary_domain ?? 'https://www.example.com';
        $ideaTitle   = $idea->title;
        $ideaSummary = $idea->summary ?? '';

        $prompt = <<<PROMPT
You are an expert marketing copywriter for a family campground.

You will write content in TWO perspectives for ONE idea:

1) INTERNAL ARTICLE (for the campground's own website: book.park.com)
   - Voice: first person plural ("we", "our campground", "our guests")
   - Example: "We added a new pool for our guests this season."
   - Tone: friendly, welcoming, family-camping style
   - Format: Markdown with a main H1 and 3–5 short sections (H2 headings)

2) EXTERNAL ARTICLE (for partner sites like rvcamping.info)
   - Voice: third person ("Kayuta Lake Campground", "they", "the park")
   - MUST mention the park name and URL clearly.
   - Example: "Kayuta Lake Campground has added a new pool for its guests this season."
   - Tone: slightly more informational/press-release style
   - Format: Markdown with a main H1 and 3–5 short sections (H2 headings)
   - Include a short credit line at the end like:
     "This article comes from Kayuta Lake Campground and Marina (https://www.kayuta.com)."

Also generate short social captions for Facebook, Instagram, and X (Twitter).

Use this idea:

TITLE: {$ideaTitle}
SUMMARY: {$ideaSummary}
PARK NAME: {$parkName}
PARK URL: {$parkUrl}

Return ONLY valid JSON (no markdown, no commentary) in exactly this structure:

{
  "internal": {
    "type": "article",
    "title": "string",
    "slug": "string",
    "body_md": "markdown string"
  },
  "external": {
    "channel": "rvcamping",
    "title": "string",
    "body_md": "markdown string",
    "meta": {
      "idea_id": number,
      "tenant_id": number,
      "park_name": "string",
      "park_url": "string",
      "credit_line": "string"
    }
  },
  "variants": [
    {
      "platform": "facebook",
      "caption": "string (max ~400 chars)",
      "media_hint": "short description of ideal hero image"
    },
    {
      "platform": "instagram",
      "caption": "string (max 2200 chars, use 1-3 emojis, some hashtags)",
      "media_hint": "short description of vertical image"
    },
    {
      "platform": "x",
      "caption": "string (max 250 chars, short and punchy)",
      "media_hint": "short description of image"
    }
  ],
  "media": [
    {
      "type": "image",
      "prompt": "short prompt describing an image that would fit this article"
    }
  ]
}

Rules:
- INTERNAL: always use "we/our" perspective.
- EXTERNAL: always use the park name and "they/their" perspective.
- Always mention {$parkName} and {$parkUrl} at least once in the EXTERNAL article.
- Do NOT put real tracking parameters or UTM in the captions.
PROMPT;

        $response = $client->chat()->create([
            'model' => 'gpt-4.1-mini', // ya jo bhi model tum use kar rahe ho
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a JSON-only content generator. Always respond with valid JSON only.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        $content = $response->choices[0]->message->content ?? '';

        $data = json_decode($content, true);

        if (! is_array($data)) {
            return [
                'internal' => [],
                'external' => [],
                'variants' => [],
                'media'    => [],
            ];
        }

        $slug = $data['internal']['slug'] ?? Str::slug($ideaTitle . '-' . $idea->id);

        $internal = [
            'type'    => $data['internal']['type']    ?? 'article',
            'title'   => $data['internal']['title']   ?? $ideaTitle,
            'slug'    => $slug,
            'body_md' => $data['internal']['body_md'] ?? '',
        ];

        $externalMeta = $data['external']['meta'] ?? [];
        $externalMeta['idea_id']    = $idea->id;
        $externalMeta['tenant_id']  = $idea->tenant_id;
        $externalMeta['park_name']  = $parkName;
        $externalMeta['park_url']   = $parkUrl;
        $externalMeta['credit_line']= $externalMeta['credit_line'] ?? "This article comes from {$parkName} ({$parkUrl}).";

        $external = [
            'channel' => $data['external']['channel'] ?? 'rvcamping',
            'title'   => $data['external']['title']   ?? ($ideaTitle . ' at ' . $parkName),
            'body_md' => $data['external']['body_md'] ?? '',
            'meta'    => $externalMeta,
        ];

        $variants = $data['variants'] ?? [];
        $media    = $data['media']    ?? [];

        return [
            'internal' => $internal,
            'external' => $external,
            'variants' => $variants,
            'media'    => $media,
        ];
    }
}
