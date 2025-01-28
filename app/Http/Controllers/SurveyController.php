<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\PublishSurveyModel;
use App\Models\Reservation;
use App\Models\SurveysResponseModel;
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

    public function responsesurvey($surveyId, $email, $siteId, $token)
    {
        
        $uniqueToken = SurveysResponseModel::where('token', $token)->first();
        $survey = PublishSurveyModel::find($surveyId);

        if ($uniqueToken) {
            return view('feedback-survey.components.success-response');

        } elseif ($survey) {
            $questions = json_decode($survey->questions, true) ?? [];
            $answerTypes = json_decode($survey->answer_types, true) ?? []; 

            $result = [];

            foreach ($questions as $index => $question) {
                $formattedAnswerTypes = [];
                foreach ($answerTypes[$index] ?? [] as $type) {
                    if (strpos($type, 'rate:') !== false) {
                        $formattedAnswerTypes[] = $type;
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
       

        

            return view('feedback-survey.response-survey', [
                'surveyId' => $surveyId,
                'result' => $result,
                'email' => $email,
                'siteId' => $siteId,
                'token' => $token
            ]);
        } else {
            return response()->json([
                'error' => 'Survey not found',
            ], 404);
        }
    }

    public function getPublishedSurvey()
    {
        $publishedSurveys = PublishSurveyModel::all();
        return response()->json($publishedSurveys);
    }

    public function updateStatus(Request $request) 
    {
        $publishedSurveys = PublishSurveyModel::find($request->id);
    
        if ($publishedSurveys) {
            $publishedSurveys->update([
                'active' => $request->isActive
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Survey status updated successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Survey not found.'
            ], 404);
        }
    }
    
    

    public function storeResponses(Request $request) {
        $request->validate([
            'email' => 'required|string|max:255',
            'siteId' => 'required|string|max:255',
            'surveyId' => 'required|integer',
        ]);

        $surveyData = array_slice($request->survey, 1);

        $data = array_map(function ($item) {
            return [
                'question' => $item['question'],
                'answer' => $item['answers'],
            ];
        },  $surveyData);

        SurveysResponseModel::create([
            'email' => $request->email,
            'siteId' => $request->siteId,
            'survey_id' => $request->surveyId,
            'questions' => json_encode(array_column($data, 'question')),
            'answers' => json_encode(array_column($data, 'answer')),
            'token' => $request->token
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Survey response saved successfully!',
        ]);
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
