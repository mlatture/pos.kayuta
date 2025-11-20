<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\ContentIdea;

class DemoContentIdeasSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 1; // filhaal 1 park / tenant

        // 1) Some demo categories
        $categoriesData = [
            [
                'name' => 'Campfire Recipes',
                'slug' => 'campfire-recipes',
                'template_prompt' => 'Family-friendly campfire recipes and cooking tips.',
            ],
            [
                'name' => 'Local Events',
                'slug' => 'local-events',
                'template_prompt' => 'Upcoming local events near the campground.',
            ],
            [
                'name' => 'Guest Stories',
                'slug' => 'guest-stories',
                'template_prompt' => 'Heartwarming or funny stories from guests.',
            ],
            [
                'name' => 'Wildlife Watch',
                'slug' => 'wildlife-watch',
                'template_prompt' => 'Wildlife sightings and nature education posts.',
            ],
        ];

        $categories = [];

        foreach ($categoriesData as $data) {
            $categories[] = Category::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'slug'      => $data['slug'],
                ],
                [
                    'name'            => $data['name'],
                    'template_prompt' => $data['template_prompt'],
                    'is_custom'       => false,
                    'created_by'      => null,
                ]
            );
        }

        // 2) Demo ideas per category
        foreach ($categories as $category) {
            // Avoid double-seeding ideas if they already exist
            if (ContentIdea::where('tenant_id', $tenantId)->where('category_id', $category->id)->exists()) {
                continue;
            }

            if ($category->slug === 'campfire-recipes') {
                ContentIdea::create([
                    'tenant_id'   => $tenantId,
                    'category_id' => $category->id,
                    'title'       => "Ultimate S'mores Night at the Campfire",
                    'summary'     => 'Easy steps for perfect campfire s’mores plus safety tips for families.',
                    'rank'        => 1,
                    'status'      => 'draft',
                    'ai_inputs'   => [
                        'tone'     => 'friendly',
                        'audience' => 'families',
                    ],
                ]);

                ContentIdea::create([
                    'tenant_id'   => $tenantId,
                    'category_id' => $category->id,
                    'title'       => 'One-Pot Campfire Chili for Crowd',
                    'summary'     => 'Hearty chili recipe perfect for group camping nights.',
                    'rank'        => 2,
                    'status'      => 'draft',
                    'ai_inputs'   => [
                        'tone'     => 'cozy',
                        'audience' => 'group-campers',
                    ],
                ]);
            }

            if ($category->slug === 'local-events') {
                ContentIdea::create([
                    'tenant_id'   => $tenantId,
                    'category_id' => $category->id,
                    'title'       => 'This Weekend at the Park: Live Music & BBQ',
                    'summary'     => 'Overview of the upcoming weekend event schedule with live music.',
                    'rank'        => 1,
                    'status'      => 'draft',
                    'ai_inputs'   => [
                        'tone'     => 'excited',
                        'audience' => 'all-guests',
                    ],
                ]);
            }

            if ($category->slug === 'guest-stories') {
                ContentIdea::create([
                    'tenant_id'   => $tenantId,
                    'category_id' => $category->id,
                    'title'       => 'How One Family Made Memories at Our Lake',
                    'summary'     => 'Short story-style post about a family weekend stay.',
                    'rank'        => 1,
                    'status'      => 'draft',
                    'ai_inputs'   => [
                        'tone'     => 'inspiring',
                        'audience' => 'families',
                    ],
                ]);
            }

            if ($category->slug === 'wildlife-watch') {
                ContentIdea::create([
                    'tenant_id'   => $tenantId,
                    'category_id' => $category->id,
                    'title'       => 'Owls After Dark: What You Might Hear at Night',
                    'summary'     => 'Educational post about hearing owls and respecting wildlife.',
                    'rank'        => 1,
                    'status'      => 'draft',
                    'ai_inputs'   => [
                        'tone'     => 'educational',
                        'audience' => 'nature-lovers',
                    ],
                ]);
            }
        }
    }
}
