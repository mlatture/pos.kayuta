<?php

namespace Database\Seeders;

use App\Models\UpsellText;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UpsellTextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function run()
    {
        $messages = [
            "All set? Got enough Firewood, Ice, Marshmallows?",
            "Got everything you need? Don't forget any last-minute essentials like firewood, ice, or bug spray!",
            "Are you all set? Need any extra snacks, drinks, or propane before you head out?",
            "All good to go? Double-check if you’ve got enough marshmallows, ice, or firewood for tonight!",
            "Anything else for the campfire? We’ve got s’mores kits, extra logs, and cold drinks if you need!",
            "Got everything? We’ve got ice, kindling, and any extra camp supplies you might need!",
            "Before you head out, need any last-minute firewood, drinks, or even sunscreen?",
            "Is that everything? Make sure you’ve got enough propane, snacks, or firewood for the night!",
            "Ready to go? We’ve got all the extras like ice, matches, and s’mores supplies if you need!",
            "Good to go? Don’t forget essentials like bug spray, extra logs, or a few cold drinks!",
            "All set? Got enough firewood, snacks, and any backup batteries for your flashlight?",
            "Anything else while you’re here? We’ve got more ice, campfire wood, and extra marshmallows!",
            "Before you head out, do you need any last-minute supplies like ice, kindling, or bug spray?",
            "All stocked up? Don't forget we’ve got propane, ice, and a few treats for the campfire!",
            "Got everything for your campfire tonight? We’ve got s’mores kits, extra firewood, and ice!",
            "Need anything else? We’ve got ice, marshmallows, and even sunscreen to keep you covered!",
            "Is there anything else? Stock up on firewood, extra drinks, or some snacks for later!",
            "Double-checking—you good on ice, snacks, firewood, and everything else for the night?",
            "Anything else for the night? We’ve got firewood, ice, and even some extra flashlights!",
            "Last chance before checkout—need any more ice, marshmallows, or firewood for tonight?",
            "All set? Just a reminder that we’ve got plenty of ice, firewood, and propane if you need!"
        ];

        foreach ($messages as $message) {
            UpsellText::create(['message_text' => $message, 'active_message' => true]);
        }
    }
}
