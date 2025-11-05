<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentHub\AiToneProfile;
use App\Models\ContentHub\ContentHubSetting;

class ContentHubSeeder extends Seeder
{
    public function run(): void
    {
        $defaultHashtags = [
          'evergreen' => ['#camping','#outdoors','#nature','#rvlife','#campground'],
          'seasonal_spring' => ['#spring','#wildflowers','#hiking','#fresh'],
          'seasonal_summer' => ['#summer','#swimming','#family','#vacation'],
          'seasonal_fall' => ['#fall','#autumn','#colors','#cozy'],
          'seasonal_winter' => ['#winter','#peaceful','#quiet','#offseason'],
          'activities' => ['#fishing','#boating','#playground','#recreation'],
        ];

        $defaultToneProfiles = [
          'website' => ['tone'=>'informative','length'=>'long','seo_focused'=>true],
          'facebook' => ['tone'=>'warm','length'=>'medium','community_focused'=>true],
          'instagram' => ['tone'=>'playful','length'=>'short','visual_focused'=>true],
          'google_business' => ['tone'=>'informative','length'=>'short','local_seo'=>true],
        ];

        ContentHubSetting::firstOrCreate(
          ['id'=>1],
          [
            'is_enabled'=>false,
            'ai_service_provider'=>'claude',
            'settings_json'=>['hashtags'=>$defaultHashtags]
          ]
        );

        foreach($defaultToneProfiles as $channel => $toneSettings){
            AiToneProfile::updateOrCreate(
              ['channel'=>$channel],
              ['tone_settings'=>$toneSettings]
            );
        }
    }
}
