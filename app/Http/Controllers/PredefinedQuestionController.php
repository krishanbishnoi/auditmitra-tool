<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PredefinedQuestion;
use App\Model\Role;

class PredefinedQuestionController extends Controller
{
    // Show the form
    public function create()
    {
        $roles = Role::all(); // fetch all roles

        return view('predefined_questions.create', compact('roles'));
    }

    public function index()
    {
        $questions = PredefinedQuestion::all();
        return view('predefined_questions.index', compact('questions'));
    }


    // Handle form submission
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'role' => 'required|string|max:255',
        ]);

        PredefinedQuestion::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'Question added successfully!');
    }

    public function edit($id)
    {
        $question = PredefinedQuestion::findOrFail($id);
        $roles = Role::pluck('name', 'name');
        return view('predefined_questions.edit', compact('question', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'role' => 'required|string',
        ]);

        $question = PredefinedQuestion::findOrFail($id);
        $question->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'role' => $request->role,
        ]);

        return redirect()->route('predefined_questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy($id)
    {
        $question = PredefinedQuestion::findOrFail($id);
        $question->delete();

        return redirect()->route('predefined_questions.index')->with('success', 'Predefined question deleted successfully.');
    }

    
}
