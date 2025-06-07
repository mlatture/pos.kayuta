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
                'user_prompt' => "Rewrite this FAQ for an RV park website. Make it natural and optimized for SEO and AEO. Avoid filler, symbols, or formatting.\n\nQuestion\n{{ question }}\n\nAnswer\n{{ answer }}",
            ],
        );
        PromptTemplate::updateOrCreate(
            ['type' => 'blog_write'],
            [
                'system_prompt' => <<<EOT
                You write engaging blog posts for an RV park website. The content should be friendly, informative, and optimized for SEO. It may include time-sensitive updates, news, event announcements, or casual stories. Make sure it's easy to read and valuable to travel and outdoor enthusiasts.

                Use plain text only. Do not use quotation marks, lists, bullets, numbered headings, symbols ,asterisks, emojis, bold, italics, or any special characters for formatting.

                Return your response strictly in this format, with no symbols or styling:

                Title: Rewritten title without quotes

                Content: Rewritten content in 2 to 6 natural paragraphs, each 3 to 6 sentences
                EOT
                ,
                'user_prompt' => <<<EOT
                Please rewrite the following blog post to be more engaging and SEO-optimized. Improve both the title and the content.

                Title: {{ title }}

                Content: {{ description }}
                EOT
            ,
            ],
        );

        PromptTemplate::updateOrCreate(
            ['type' => 'article_write'],
            [
                'system_prompt' => <<<EOT
                You are a helpful assistant that rewrites both the title and content of an article for an RV campground website.
                Make the title more descriptive and SEO-friendly.

                The content must be evergreen, clear, and optimized for SEO and AEO.
                Rewrite it into **2 to 8 paragraphs**, with each paragraph containing **3 to 8 sentences**.
                Avoid markdown, emojis, lists, or symbols. Use plain text only. Do not return anything except the rewritten title and content.

                Return your response strictly in this format:

                Title: [rewritten title]

                Content: [rewritten article]
                EOT
                ,
                'user_prompt' => <<<EOT
                Please rewrite the following article to be more engaging and SEO-optimized. Improve both the title and the content.

                Title: {{ title }}

                Content: {{ description }}
                EOT
            ,
            ],
        );

        PromptTemplate::updateOrCreate(
            ['type' => 'page_write'],
            [
                'system_prompt' => <<<EOT
                You write static content pages for an RV park website. These include About, Contact, Amenities, and other similar pages.

                The tone must be professional, clear, welcoming, and SEO-optimized. Do not include emojis, markdown, special characters, styling tags, bullets, lists, or headings.

                Use plain text only. Make the writing easy to scan and focused on the page’s purpose.

                Return your response strictly in this format:

                Title: Rewritten page title without quotes

                Content: Rewritten content in 2 to 6 paragraphs, each with 3 to 6 natural sentences
                EOT
                ,
                'user_prompt' => <<<EOT
                Please rewrite the following static page content for an RV park website. Improve both the title and content.

                Title: {{ title }}

                Content: {{ description }}
                EOT
            ,
            ],
        );
    }
}
