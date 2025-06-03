<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PromptTemplate;

class PromptTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PromptTemplate::updateOrCreate(
            ['type' => 'faq_rewrite'],
            [
                'system_prompt' => "You rewrite FAQ content for an RV park website. Make it clear, natural, and optimized for SEO and AEO. Add helpful context if relevant.\n\nUse plain text only. Do not use markdown, emojis, emphasis, dashes, colons, semicolons, lists, or headers. Avoid filler, repeated questions, and robotic phrases like \"in conclusion\" or \"keep in mind\".",
                'user_prompt' => "Rewrite this FAQ for an RV park website. Make it natural and optimized for SEO and AEO. Avoid filler, symbols, or formatting.\n\nQuestion\n{{question}}\n\nAnswer\n{{answer}}"
            ]
        );
        
    }
}
