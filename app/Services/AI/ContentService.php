<?php
// app/Services/AI/ContentService.php

namespace App\Services\AI;

use App\Models\Category;
use App\Models\ContentIdea;
use App\Models\TenantSetting;

class ContentService
{
    public function generateIdeasForTenant(int $tenantId): void
    {
        $categories = Category::where('tenant_id', $tenantId)->get();

        foreach ($categories as $category) {
            $this->generateIdeasForCategory($tenantId, $category);
        }
    }

    protected function generateIdeasForCategory(int $tenantId, Category $category): void
    {
        // Call your AI provider here (OpenAI/Claude/etc.)
        // For now, stub fake ideas:

        $ideas = $this->callAIForIdeas($tenantId, $category);

        foreach ($ideas as $rank => $idea) {
            ContentIdea::create([
                'tenant_id'   => $tenantId,
                'category_id' => $category->id,
                'title'       => $idea['title'],
                'summary'     => $idea['summary'] ?? null,
                'rank'        => $rank + 1,
                'status'      => 'draft',
                'ai_inputs'   => $idea['inputs'] ?? null,
            ]);
        }
    }

    protected function callAIForIdeas(int $tenantId, Category $category): array
    {
        // Yahan tum actual prompt banaoge
        // Abhi ke liye pseudo-response:
        return [
            [
                'title'   => "Campfire S'mores Night at the Lake",
                'summary' => 'Simple family-friendly campfire recipe and tips',
                'inputs'  => [
                    'category' => $category->slug,
                    'tone'     => 'friendly',
                ],
            ],
            [
                'title'   => 'Wildlife Watch: Owls After Dark',
                'summary' => 'Educational post about hearing owls near the park',
                'inputs'  => [],
            ],
            [
                'title'   => 'Top 5 Weekend Activities for Families',
                'summary' => 'Guide-style content for weekend guests',
                'inputs'  => [],
            ],
        ];
    }
}
