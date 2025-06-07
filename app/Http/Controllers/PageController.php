<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Blogs;
use App\Models\Article;
use App\Models\PromptTemplate;
use Carbon\Carbon;
use Illuminate\Support\Str;
use OpenAI;
class PageController extends Controller
{
    //

    public function index(Request $request)
    {
        $type = $request->query('type', 'page');
        $blogs = null;
        $pages = null;
        $articles = null;

        if ($type === 'blog' || $type === 'blogs') {
            $blogs = Blogs::all();
        } elseif ($type === 'article' || $type === 'articles') {
            $articles = Article::all();
        } else {
            $pages = Page::where('type', $type)->get();
        }

        return view('blogs.index', compact('pages', 'type', 'blogs', 'articles'));
    }

    public function create(Request $request)
    {
        $type = $request->type;

        switch ($type) {
            case 'page':
                return view('pages.create', compact('type'));
            case 'article':
                return view('articles.create', compact('type'));
            case 'blog':
                return view('blogs.create', compact('type'));
        }

        return view('blogs.create', compact('type'));
    }

    public function storeBlogs(Request $request)
    {
        $data = $request->except(['thumbnail', 'opengraphimage']);

        $data['image'] = $request->file('thumbnail');
        $data['opengraphimage'] = $request->file('opengraphimage');

        Blogs::create($data);

        return redirect()->back()->with('success', 'Blog created!');
    }

    public function storeArticle(Request $request)
    {
        $data = $request->except(['thumbnail', 'opengraphimage']); 

        $data['thumbnail'] = $request->file('thumbnail');
        $data['opengraphimage'] = $request->file('opengraphimage');

        Article::create($data);

        return redirect()->back()->with('success', 'Article created!');
    }

    public function storePages(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image',
            'attachment' => 'nullable|file',
            'opengraphimage' => 'nullable|image',
        ]);

        $data = $request->all();
        $data['image'] = $request->file('image');
        $data['attachment'] = $request->file('attachment');
        $data['opengraphimage'] = $request->file('opengraphimage');

        $data['schema_code_pasting'] = json_encode(
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => $request->title,
                'description' => strip_tags($request->description),
                'url' => $request->canonicalurl ?: url('/pages/' . Str::slug($request->title)),
            ],
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT,
        );

        Page::create($data);

        return redirect()->back()->with('success', 'Page created!');
    }

    public function aiRewriteArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'type' => 'required|in:article,blogs,page',
        ]);

        $title = trim($request->input('title'));
        $description = trim($request->input('description'));

        if ($request->type === 'article') {
            $promptType = 'article_write';
        } elseif ($request->type === 'blogs') {
            $promptType = 'blog_write';
        } elseif ($request->type === 'page') {
            $promptType = 'page_write';
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid type specified.'], 400);
        }

        $prompt = PromptTemplate::where('type', $promptType)->first();

        $systemPrompt = $prompt->system_prompt;
        $userPrompt = $prompt->user_prompt;

        $aiResponse = $this->callOpenAI($systemPrompt, $userPrompt);

        return response()->json([
            'success' => true,
            'title' => $aiResponse['title'] ?? $title,
            'description' => $aiResponse['description'] ?? $description,
        ]);
    }

    protected function callOpenAI(string $systemPrompt, string $userPrompt): array
    {
        try {
            $client = OpenAI::client(env('OPENAI_API_KEY'));

            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [['role' => 'system', 'content' => $systemPrompt], ['role' => 'user', 'content' => $userPrompt]],
                'temperature' => 0.7,
                'max_tokens' => 700,
            ]);

            $content = trim($response->choices[0]->message->content ?? '');

            $title = '';
            $description = '';

            if (preg_match('/Title[:\n]*([\s\S]*?)Content[:\n]+([\s\S]*)/i', $content, $matches)) {
                $title = trim($matches[1]);
                $description = trim($matches[2]);
            } else {
                $description = $content;
            }

            return [
                'title' => $title,
                'description' => $description,
            ];
        } catch (\Exception $e) {
            return [
                'title' => '',
                'description' => 'AI failed: ' . $e->getMessage(),
            ];
        }
    }

    public function editPage($id)
    {
        $page = Page::findOrFail($id);
        return view('pages.edit', compact('page'));
    }

    public function updatePage(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'metatitle' => 'nullable|string',
            'metadescription' => 'nullable|string',
            'canonicalurl' => 'nullable|url',
            'opengraphtitle' => 'nullable|string',
            'opengraphdescription' => 'nullable|string',
            'schema_code_pasting' => 'nullable|string',
            'image' => 'nullable|image',
            'opengraphimage' => 'nullable|image',
            'attachment' => 'nullable|file',
        ]);

        $page->fill($request->except(['image', 'opengraphimage', 'attachment', 'status']));
        $page->status = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            $page->image = $request->file('image')->store('storage/pages', 'public');
        }

        if ($request->hasFile('opengraphimage')) {
            $page->opengraphimage = $request->file('opengraphimage')->store('storage/og', 'public');
        }

        if ($request->hasFile('attachment')) {
            $page->attachment = $request->file('attachment')->store('storage/attachments', 'public');
        }

        $page->save();

        return redirect()->route('pages.index')->with('success', 'Page updated successfully.');
    }

    public function editArticle($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.edit', compact('article'));
    }

    public function updateArticle(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
        ]);

        $article->title = $request->title;
        $article->slug = Str::slug($request->title);
        $article->description = $request->description;
        $article->status = $request->has('status') ? 1 : 0;

        if ($request->hasFile('thumbnail')) {
            $article->thumbnail = $request->file('thumbnail')->store('storage/articles', 'public');
        }

        $article->save();

        return redirect()
            ->route('pages.index', ['type' => 'article'])
            ->with('success', 'Article updated successfully!');
    }

    public function editBlogs($id)
    {
        $blog = Blogs::findOrFail($id);
        return view('blogs.edit', compact('blog'));
    }

    public function updateBlogs(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $blog = Blogs::findOrFail($id);
        $blog->title = $request->title;
        $blog->description = $request->description;
        $blog->status = $request->has('status') ? 1 : 0;

        if ($request->hasFile('thumbnail')) {
            $blog->thumbnail = $request->file('thumbnail')->store('storage/blogs', 'public');
        }

        $blog->save();

        return redirect()
            ->route('pages.index', ['type' => 'blog'])
            ->with('success', 'Article updated successfully!');
    }

    public function destroyBlogs($id)
    {
        $blog = Blogs::findOrFail($id);
        $blog->delete();

        return redirect()
            ->route('pages.index', ['type' => 'blog'])
            ->with('success', 'Blogs deleted successfully!');
    }

    public function destroyArticle($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()
            ->route('pages.index', ['type' => 'article'])
            ->with('success', 'Article deleted successfully!');
    }

    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return redirect()->route('pages.index')->with('Page deleted successfully!');
    }
}
