<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\PublishSurveyModel;
use App\Models\Reservation;
class SurveyController extends Controller
{
    public function index()
    {
        return view('feedback-survey.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'data' => 'required|array|min:1',
            'data.*.questions' => 'required|string|max:255',
          
        ]);

        $questions = array_map(function ($item) {
            return [
                'questions' => $item['questions'],
                'answer_types' => $item['answer_types']
            ];
        }, $request->data);
       
        PublishSurveyModel::create([
            'title' => $request->title,
            'questions' => json_encode(array_column($questions, 'questions')), 
            'answer_types' => json_encode(array_column($questions, 'answer_types')),
        ]);
        
        
        return response()->json(['success' => true, 'message' => 'Survey created successfully!'], 200);
    }

    public function responsesurvey($surveyId)
{
    $survey = PublishSurveyModel::find($surveyId);

    if ($survey) {
        $questions = json_decode($survey->questions, true) ?? [];
        $answerTypes = json_decode($survey->answer_types, true) ?? []; 

        $result = [];

        foreach ($questions as $index => $question) {
            $formattedAnswerTypes = [];
            foreach ($answerTypes[$index] ?? [] as $type) {
                if (is_array($type) && isset($type['rate'])) {
                    $formattedAnswerTypes[] = "rate({$type['rate']})";
                } elseif ($type === 'radiobutton') {
                    $formattedAnswerTypes[] = 'radiobutton';
                } elseif ($type === 'comments') {
                    $formattedAnswerTypes[] = 'comments';
                }
            }

            $result[] = [
                'questions' => $question,
                'answer_types' => $formattedAnswerTypes,
            ];
        }

      

        return view('feedback-survey.response-survey', compact('result'));
    } else {
        return response()->json([
            'error' => 'Survey not found',
        ], 404);
    }
}

    

    // public function edit(Survey $survey)
    // {
    //     return view('surveys.edit', compact('survey'));
    // }

    // public function update(Request $request, Survey $survey)
    // {
    //     $request->validate([
    //         'guest_email' => 'required|email',
    //         'subject' => 'required|string|max:255',
    //         'message' => 'required|string',
    //     ]);

    //     $survey->update($request->all());

    //     return redirect()->back()->with('success', 'Survey updated successfully!');
    // }

    // public function destroy(Survey $survey)
    // {
    //     $survey->delete();

    //     return redirect()->back()->with('success', 'Survey deleted successfully!');
    // }
}
