<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PredefinedQuestion;


class HelpChatController extends Controller
{
    //
    public function index()
{
    $user = auth()->user();

    // If user is logged in, get role, otherwise default to 'guest'
    $role = $user ? $user->role : 'guest';

    // Fetch questions filtered by the user's role
    $questions = \App\PredefinedQuestion::where('role', $role)->get();

    return view('chat.faq', compact('questions'));
}


    // Handle AJAX request to get the answer
    public function getAnswer(Request $request)
{
    $question = \App\PredefinedQuestion::findOrFail($request->id);
    $user = auth()->user();

    $userMessage = $question->question;
    $botReply = $question->answer;

    // Get current chat from session or initialize
    $chat = session()->get('chat_messages', []);

    // Add user message
    $chat[] = ['sender' => 'user', 'message' => $userMessage];

    // Add bot reply
    $chat[] = ['sender' => 'bot', 'message' => $botReply];

    // Save updated chat back to session
    session(['chat_messages' => $chat]);

    return response()->json(['answer' => $botReply]);
}

}
