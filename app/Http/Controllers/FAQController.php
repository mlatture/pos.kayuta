<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Infos;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
