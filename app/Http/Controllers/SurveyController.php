<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
class SurveyController extends Controller
{
    public function create()
    {
        return view('surveys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Survey::create($request->all());

        return redirect()->back()->with('success', 'Survey created successfully!');
    }

    public function edit(Survey $survey)
    {
        return view('surveys.edit', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        $request->validate([
            'guest_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $survey->update($request->all());

        return redirect()->back()->with('success', 'Survey updated successfully!');
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();

        return redirect()->back()->with('success', 'Survey deleted successfully!');
    }
}
