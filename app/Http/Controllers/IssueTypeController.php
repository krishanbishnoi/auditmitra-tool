<?php

namespace App\Http\Controllers;

use App\HelpTopic;
use App\IssueType;
use Illuminate\Http\Request;

class IssueTypeController extends Controller
{
    public function index()
    {
        // Get all issue types along with their related help topic
        $issueTypes = IssueType::with('helpTopic')->get();
        return view('issue_types.index', compact('issueTypes'));
    }

    public function create()
    {
        // Get all help topics to display in the select dropdown
        $helpTopics = HelpTopic::all();
        return view('issue_types.create', compact('helpTopics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'help_topic_id' => 'required|exists:help_topics,id'
        ]);

        IssueType::create($request->all());

        return redirect()->route('issue_types.index')->with('success', 'Issue Type created successfully');
    }

    public function edit($id)
    {
        // Get the issue type and all help topics
        $issueType = IssueType::findOrFail($id);
        $helpTopics = HelpTopic::all();
        return view('issue_types.edit', compact('issueType', 'helpTopics'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'help_topic_id' => 'required|exists:help_topics,id'
        ]);

        $issueType = IssueType::findOrFail($id);
        $issueType->update($request->all());

        return redirect()->route('issue_types.index')->with('success', 'Issue Type updated successfully');
    }

    public function destroy($id)
    {
        $issueType = IssueType::findOrFail($id);
        $issueType->delete();

        return redirect()->route('issue_types.index')->with('success', 'Issue Type deleted successfully');
    }
}
