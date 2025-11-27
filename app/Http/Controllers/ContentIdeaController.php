<?php

namespace App\Http\Controllers;

use App\Models\ContentIdea;
use App\Models\Category;
use Illuminate\Http\Request;

class ContentIdeaController extends Controller
{
    public function index()
    {
        $contentIdeas = ContentIdea::with(['category', 'tenant'])
            ->latest()
            ->paginate(20);

        return view('content-idea.index', compact('contentIdeas'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('content-idea.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title'       => 'required|string|max:255',
            'summary'     => 'nullable|string',
            'rank'        => 'nullable|integer|min:0',
            'status'      => 'nullable|string|max:50',
            'ai_inputs'   => 'nullable|string'
        ]);

        // Parse JSON
        if ($request->ai_inputs) {
            $decoded = json_decode($request->ai_inputs, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors([
                    'ai_inputs' => 'AI Inputs must be valid JSON.'
                ])->withInput();
            }
            $data['ai_inputs'] = $decoded;
        }

        // Auto tenant
        if (auth()->check()) {
            $data['tenant_id'] = auth()->user()->tenant_id ?? null;
        }

        ContentIdea::create($data);

        return redirect()->route('content-ideas.index')
            ->with('success', 'Content Idea created.');
    }

    public function edit(ContentIdea $contentIdea)
    {
        $categories = Category::orderBy('name')->get();

        return view('content-idea.edit', compact('contentIdea', 'categories'));
    }

    public function update(Request $request, ContentIdea $contentIdea)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title'       => 'required|string|max:255',
            'summary'     => 'nullable|string',
            'rank'        => 'nullable|integer|min:0',
            'status'      => 'nullable|string|max:50',
            'ai_inputs'   => 'nullable|string'
        ]);

        // Parse JSON
        if ($request->ai_inputs) {
            $decoded = json_decode($request->ai_inputs, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors([
                    'ai_inputs' => 'AI Inputs must be valid JSON.'
                ])->withInput();
            }
            $data['ai_inputs'] = $decoded;
        } else {
            $data['ai_inputs'] = null;
        }

        $contentIdea->update($data);

        return redirect()->route('content-ideas.index')
            ->with('success', 'Content Idea updated.');
    }

    public function destroy(ContentIdea $contentIdea)
    {
        $contentIdea->delete();

        return redirect()->route('content-ideas.index')
            ->with('success', 'Content Idea deleted.');
    }
}
