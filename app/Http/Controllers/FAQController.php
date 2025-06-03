<?php

namespace App\Http\Controllers;

use App\Models\Infos;
use App\Models\PromptTemplate;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;
use OpenAI;

class FAQController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $faqs = Infos::select('id', 'title', 'description', 'status', 'show_in_details', 'created_at')->latest();

            return DataTables::of($faqs)
                ->addIndexColumn()
                ->addColumn('actions', function ($faq) {
                    $editButton = auth()->user()->hasPermission(config('constants.role_modules.edit_faqs.value')) ? '<a href="' . route('faq.edit', $faq->id) . '" class="btn btn-primary"><i class="fas fa-edit"></i></a>' : '';
                    $deleteButton = auth()->user()->hasPermission(config('constants.role_modules.delete_faqs.value')) ? '<button class="btn btn-danger btn-delete" data-url="' . route('faq.destroy', $faq->id) . '"><i class="fas fa-trash"></i></button>' : '';
                    return $editButton . ' ' . $deleteButton;
                })
                ->editColumn('description', fn($faq) => Str::limit(strip_tags($faq->description), 50, '...'))
                ->editColumn('created_at', fn($faq) => Carbon::parse($faq->created_at)->format('F j, Y'))
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('faq.index');
    }

    public function create()
    {
        return view('faq.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order_by' => 'nullable|integer|min:1',
        ]);

        Infos::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'order_by' => $validated['order_by'],
            'auto_correct' => $request->has('auto_correct'),
            'ai_rewrite' => $request->has('ai_rewrite'),
            'show_in_details' => $request->has('show_in_details'),
            'status' => $request->has('status'),
        ]);

        return redirect()->route('faq.index')->with('success', 'FAQ created successfully');
    }

    public function grammarCorrect(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        $correctedQuestion = $this->checkGrammar($request->question);
        $correctedAnswer = $this->checkGrammar($request->answer);

        return response()->json([
            'success' => true,
            'question' => $correctedQuestion,
            'answer' => $correctedAnswer,
        ]);
    }

    protected function checkGrammar($text)
    {
        $response = \Http::asForm()->post('https://api.languagetool.org/v2/check', [
            'text' => $text,
            'language' => 'en-US',
        ]);

        $matches = $response->json()['matches'] ?? [];

        usort($matches, fn($a, $b) => $b['offset'] <=> $a['offset']);

        foreach ($matches as $match) {
            if (isset($match['replacements'][0]['value'])) {
                $replacement = $match['replacements'][0]['value'];
                $offset = $match['offset'];
                $length = $match['length'];
                $text = substr($text, 0, $offset) . $replacement . substr($text, $offset + $length);
            }
        }

        return $text;
    }

    public function aiRewrite(Request $request)
    {
        $request->validate([
            'question' => 'nullable|string',
            'answer' => 'nullable|string',
        ]);

        $question = trim($request->input('question', ''));
        $answer = trim($request->input('answer', ''));

        $template = PromptTemplate::where('type', 'faq_rewrite')->first();

        if (!$template) {
            $template = new \stdClass();
            $template->system_prompt = "You are a helpful assistant that rewrites FAQ content for an RV site.";
            $template->user_prompt = "{{answer}}";
        }

        $filledPrompt = str_replace(
            ['{{question}}', '{{answer}}'],
            [$question, $answer],
            $template->user_prompt
        );

        $aiResponse = $this->callOpenAI($template->system_prompt, $filledPrompt);

        return response()->json([
            'success' => true,
            'question' => $aiResponse['question'] ?? $question,
            'answer' => $aiResponse['answer'] ?? $answer,
        ]);
    }

    protected function callOpenAI(string $systemPrompt, string $userPrompt): array
    {
        try {
            $client = OpenAI::client(env('OPENAI_API_KEY'));

            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            $content = trim($response->choices[0]->message->content ?? '');

            $question = '';
            $answer = '';

            if (preg_match('/Question[:\n]*([\s\S]*?)Answer[:\n]+([\s\S]*)/i', $content, $matches)) {
                $question = trim($matches[1]);
                $answer = trim($matches[2]);
            } else {
                $answer = $content;
            }

            return [
                'question' => $question,
                'answer' => $answer,
            ];
        } catch (\Exception $e) {
            return [
                'question' => '',
                'answer' => 'AI failed: ' . $e->getMessage(),
            ];
        }
    }

    public function edit($id)
    {
        $faq = Infos::findOrFail($id);
        return view('faq.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $faq = Infos::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order_by' => 'nullable|integer|min:1',
        ]);

        $faq->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'order_by' => $data['order_by'] ?? 1,
            'auto_correct' => $request->has('auto_correct'),
            'ai_rewrite' => $request->has('ai_rewrite'),
            'show_in_details' => $request->has('show_in_details'),
            'status' => $request->has('status'),
        ]);

        return redirect()->route('faq.index')->with('success', 'FAQ updated successfully.');
    }
}
