<?php

namespace App\Http\Controllers;
use App\HelpTopic;
use App\IssueType;
use App\SupportTicket;
use Auth;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    // Display a listing of the tickets
    public function index()
{
    if (Auth::user()->hasRole('Super Admin')) {
        // Super Admin can view all support tickets
        $tickets = SupportTicket::all();
    } else {
        // Other users see only their own tickets
        $tickets = SupportTicket::where('support_id', Auth::id())->get();
    }

    return view('support_tickets.index', compact('tickets'));
}


    // Show the form for creating a new ticket
    public function create()
{
    // Fetch all Help Topics from the database
    $helpTopics = HelpTopic::all();

    // Pass Help Topics to the view
    return view('support_tickets.create', compact('helpTopics'));
}


    // Store a newly created ticket
   public function store(Request $request)
{
    // Validate the incoming request data
    $request->validate([
        'help_topic' => 'required|exists:help_topics,id',  // Ensure help topic exists
        'issue_type' => 'required|exists:issue_types,id',  // Ensure issue type exists
        'subject' => 'required|string',
        'priority' => 'required|string',
        'description' => 'required|string',
    ]);

    // Fetch the Help Topic and Issue Type names
    $helpTopic = HelpTopic::find($request->help_topic);
    $issueType = IssueType::find($request->issue_type);

    // Store the data
    $supportTicket = new SupportTicket();
    $supportTicket->help_topic = $helpTopic->name;  // Store the name instead of the ID
    $supportTicket->issue_type = $issueType->name;  // Store the name instead of the ID
    $supportTicket->subject = $request->subject;
    $supportTicket->priority = $request->priority;
    $supportTicket->description = $request->description;
    
    // Set the default status to 'Open'
    $supportTicket->status = 'Open';

    // Set the authenticated user as the support ID
    $supportTicket->support_id = Auth::id();

    // Save the ticket to the database
    $supportTicket->save();

    // Redirect back with a success message
    return redirect()->route('support_tickets.create')->with('success', 'Support Ticket Created Successfully!');
}


    // Display the specified ticket
    public function show(SupportTicket $supportTicket)
    {
        return view('support_tickets.show', compact('supportTicket'));
    }

    // Show the form for editing the specified ticket
    public function edit(SupportTicket $supportTicket)
    {
        return view('support_tickets.edit', compact('supportTicket'));
    }

    // Update the specified ticket
    public function update(Request $request, SupportTicket $supportTicket)
    {
        $request->validate([
            'help_topic' => 'required|string',
            'issue_type' => 'required|string',
            'subject' => 'required|string',
            'priority' => 'required|in:Low,Medium,High',
            'description' => 'required|string',
        ]);

        $supportTicket->update($request->all());

        return redirect()->route('support_tickets.index');
    }

    public function getIssueTypes(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'help_topic_id' => 'required|exists:help_topics,id',
    ]);

    // Fetch the issue types based on the selected help topic
    $issueTypes = IssueType::where('help_topic_id', $request->help_topic_id)->get();

    return response()->json(['issue_types' => $issueTypes]);
}



    // Remove the specified ticket
    public function destroy(SupportTicket $supportTicket)
    {
        $supportTicket->delete();

        return redirect()->route('support_tickets.index');
    }

    
 public function showCloseForm($id)
{
    $ticket = SupportTicket::findOrFail($id);  
    return view('support_tickets.close', compact('ticket'));
}

public function close(Request $request, $id)
{
    $ticket = SupportTicket::findOrFail($id);
    $ticket->status = 'Closed';

    if ($request->filled('feedback')) {
        $ticket->closure_feedback = $request->input('feedback');
    }

    $ticket->save();

    return redirect()->route('support_tickets.index')->with('success', 'Ticket closed successfully.');
}






}
