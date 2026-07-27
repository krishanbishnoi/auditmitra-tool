<?php

namespace App\Http\Controllers;

use App\HelpTopic;  
use Illuminate\Http\Request;

class HelpTopicController extends Controller
{
    public function index()
    {
        $helpTopics = HelpTopic::all();
        return view('help_topics.index', compact('helpTopics'));
    }

    public function create()
    {
        return view('help_topics.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:help_topics,name'
        ]);

        HelpTopic::create($request->all());

        return redirect()->route('help_topics.index')->with('success', 'Help Topic created successfully');
    }

    public function edit($id)
    {
        $helpTopic = HelpTopic::findOrFail($id);
        return view('help_topics.edit', compact('helpTopic'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:help_topics,name,' . $id
        ]);

        $helpTopic = HelpTopic::findOrFail($id);
        $helpTopic->update($request->all());

        return redirect()->route('help_topics.index')->with('success', 'Help Topic updated successfully');
    }

    public function destroy($id)
    {
        $helpTopic = HelpTopic::findOrFail($id);
        $helpTopic->delete();

        return redirect()->route('help_topics.index')->with('success', 'Help Topic deleted successfully');
    }
}
