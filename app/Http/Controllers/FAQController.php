<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Infos;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;
use OpenAI;
class FAQController extends Controller
{
    //

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
                ->editColumn('description', function ($faq) {
                    $plainText = strip_tags($faq->description);
                    return Str::limit($plainText, 50, '...');
                })
                ->editColumn('created_at', function ($faq) {
                    return Carbon::parse($faq->created_at)->format('F j, Y');
                })
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

        $correctedQuestion = $request->question;
        $correctedAnswer = $request->answer;

        // Grammar check for question
        $questionResponse = \Http::asForm()->post('https://api.languagetool.org/v2/check', [
            'text' => $correctedQuestion,
            'language' => 'en-US',
        ]);

        $qMatches = $questionResponse->json()['matches'] ?? [];

        usort($qMatches, fn($a, $b) => $b['offset'] <=> $a['offset']);
        foreach ($qMatches as $match) {
            if (isset($match['replacements'][0]['value'])) {
                $replacement = $match['replacements'][0]['value'];
                $offset = $match['offset'];
                $length = $match['length'];

                $correctedQuestion = substr($correctedQuestion, 0, $offset) . $replacement . substr($correctedQuestion, $offset + $length);
            }
        }

        // Grammar check for answer
        $answerResponse = \Http::asForm()->post('https://api.languagetool.org/v2/check', [
            'text' => $correctedAnswer,
            'language' => 'en-US',
        ]);

        $aMatches = $answerResponse->json()['matches'] ?? [];

        usort($aMatches, fn($a, $b) => $b['offset'] <=> $a['offset']);
        foreach ($aMatches as $match) {
            if (isset($match['replacements'][0]['value'])) {
                $replacement = $match['replacements'][0]['value'];
                $offset = $match['offset'];
                $length = $match['length'];

                $correctedAnswer = substr($correctedAnswer, 0, $offset) . $replacement . substr($correctedAnswer, $offset + $length);
            }
        }

        return response()->json([
            'success' => true,
            'question' => $correctedQuestion,
            'answer' => $correctedAnswer,
        ]);
    }

    public function rewriteAnswer(Request $request)
    {
        $request->validate([
            'question' => 'nullable|string',
            'answer' => 'nullable|string',
        ]);

        $question = trim($request->input('question', ''));
        $answer = trim($request->input('answer', ''));

        // if (empty($question) && empty($answer)) {
        //     return response()->json(
        //         [
        //             'success' => false,
        //             'message' => 'Both question and answer cannot be empty.',
        //         ],
        //         400,
        //     );
        // }

        try {
            $client = OpenAI::client(env('OPENAI_API_KEY'));

            $prompt = "Rewrite the following FAQ content to be clear, concise, and SEO-friendly:\n\n";
            if ($question) {
                $prompt .= "Question: {$question}\n\n";
            }
            if ($answer) {
                $prompt .= "Answer: {$answer}";
            }

            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that rewrites FAQ content to be clear, concise, and SEO-friendly for a campground website. Do not use markdown. Plain text only.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            $rewritten = $response->choices[0]->message->content ?? '';

            // Basic parsing
            $newQuestion = '';
            $newAnswer = '';

            if (stripos($rewritten, 'Answer:') !== false) {
                [$newQuestion, $newAnswer] = explode('Answer:', $rewritten . 'Answer:');
            } else {
                $newAnswer = $rewritten;
            }

            return response()->json([
                'success' => true,
                'question' => trim(str_replace('Question:', '', $newQuestion)),
                'answer' => trim($newAnswer),
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'AI rewrite failed: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function edit($id)
    {
        $faq = Infos::find($id);
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
