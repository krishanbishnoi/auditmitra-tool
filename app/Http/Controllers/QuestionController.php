<?php

namespace App\Http\Controllers;

// app/Http/Controllers/QuestionController.php

use App\QmSheetSubParameter; // or whatever your model name is
use App\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function create($sub_param_id)
{
    $subParam = QmSheetSubParameter::findOrFail($sub_param_id);
    $questions = Question::where('sub_parameter_id', $sub_param_id)->get();

    return view('questions.create', compact('subParam', 'questions'));
}

    public function store(Request $request)
    {
        // dd($request->question_text);
        $request->validate([
            'sub_parameter_id' => 'required|exists:qm_sheet_sub_parameters,id',
            'question_text' => 'required|string|min:5',
        ]);

        Question::create([
            'sub_parameter_id' => $request->sub_parameter_id,
            'question_text' => $request->question_text,
        ]);

        return redirect()->back()->with('success', 'Question added successfully.');
    }
    public function edit($id)
{
    $question = Question::findOrFail($id);
    $subParam = QmSheetSubParameter::findOrFail($question->sub_parameter_id);

    return view('questions.edit', compact('question', 'subParam'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'question_text' => 'required|string|min:5',
    ]);

    $question = Question::findOrFail($id);
    $question->update([
        'question_text' => $request->question_text,
    ]);

    return redirect()->route('create_question', $question->sub_parameter_id)->with('success', 'Question updated successfully.');
}

public function destroy($id)
{
    $question = Question::findOrFail($id);
    $question->delete();

    return redirect()->back()->with('success', 'Question deleted successfully.');
}
}

