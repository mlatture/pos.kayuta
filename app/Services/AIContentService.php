<?php

namespace App\Services;

use App\Models\ContentIdea;
use OpenAI; // yahi wala package use hoga
use Illuminate\Support\Str;

class AIContentService
{
    public function expandIdea(ContentIdea $idea): array
    {
        $client = OpenAI::client(config('services.openai.key'));

        $parkName   = $idea->tenant->name ?? 'our campground';
        $parkUrl    = $idea->tenant->domain ?? 'https://www.example.com';
        $ideaTitle  = $idea->title;
        $ideaSummary= $idea->summary ?? '';

        // 👇 yahan hum AI ko bol rahe hain ke woh JSON structure de
        $prompt = <<<PROMPT
You are an expert marketing copywriter for a family campground.

Write content based on this idea:

TITLE: {$ideaTitle}
SUMMARY: {$ideaSummary}
PARK NAME: {$parkName}
PARK URL: {$parkUrl}

Return ONLY valid JSON (no markdown, no explanations) in this exact structure:

{
  "internal": {
    "type": "article",
    "title": "...",
    "slug": "...",
    "body_md": "..."
  },
  "external": {
    "channel": "rvcamping",
    "title": "...",
    "body_md": "...",
    "meta": {
      "idea_id": <number>,
      "tenant_id": <number>,
      "park_name": "...",
      "park_url": "...",
      "credit_line": "This article comes from ..."
    }
  },
  "variants": [
    {
      "platform": "facebook",
      "caption": "...",
      "media_hint": "hero image of campground pool at sunset"
    },
    {
      "platform": "instagram",
      "caption": "...",
      "media_hint": "vertical photo suggestion"
    },
    {
      "platform": "x",
      "caption": "short version for X / Twitter",
      "media_hint": "same hero image"
    }
  ],
  "media": [
    {
      "type": "image",
      "prompt": "describe image AI should generate for this article"
    }
  ]
}

Rules:
- "internal.body_md" must be Markdown with headings and 3–5 short sections.
- "external.body_md" must be written in third-person voice and mention {$parkName} and {$parkUrl}.
- Social captions must be friendly, family-camping tone, include 1–3 emojis max.
- Do NOT include real URLs with UTM; just refer to the main article link.
PROMPT;

        // ⚠️ yahan pe model ka naam aap jo use kar rahe ho woh daalna:
        $response = $client->chat()->create([
            'model' => 'gpt-4.1-mini', // ya jo bhi tum use karna chaho
            'messages' => [
                ['role' => 'system', 'content' => 'You are a JSON-only content generator. Always respond with valid JSON.'],
                ['role' => 'user',   'content' => $prompt],
            ],
        ]);

        $content = $response->choices[0]->message->content ?? '';
        

        // JSON parse
        $data = json_decode($content, true);

        // agar JSON tut gaya ho to null aayega
        if (! is_array($data)) {
            // fallback empty structure, baad mein ContentExpansionService handle karega
            return [
                'internal' => [],
                'external' => [],
                'variants' => [],
                'media'    => [],
            ];
        }

        // id/tenant add ensure karein
        $slug = $data['internal']['slug'] ?? Str::slug($ideaTitle . '-' . $idea->id);

        // normalize
        $internal = [
            'type'    => $data['internal']['type']    ?? 'article',
            'title'   => $data['internal']['title']   ?? $ideaTitle,
            'slug'    => $slug,
            'body_md' => $data['internal']['body_md'] ?? '',
        ];

        $externalMeta = $data['external']['meta'] ?? [];
        $externalMeta['idea_id']   = $idea->id;
        $externalMeta['tenant_id'] = $idea->tenant_id;
        $externalMeta['park_name'] = $parkName;
        $externalMeta['park_url']  = $parkUrl;

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
