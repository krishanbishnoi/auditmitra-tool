<?php

namespace App\Http\Controllers\Legal;

use App\Http\Controllers\Controller;
use App\Advocate;
use App\LegalCycle;
use Illuminate\Http\Request;
use Auth;

class CycleController extends Controller
{
    public function createCycle(Request $request)
    {
        if ($request->isMethod('post')) {
            if ($request->cycle_name) {
                $data = [];
                $data['created_by'] = Auth::user()->id;
                $data['client_id'] = Auth::user()->id;
                $data['name'] = $request->cycle_name;

                // Check uniqueness for the current client only
                $has = LegalCycle::where('name', $data['name'])
                    ->where('client_id', Auth::user()->id)
                    ->first();

                if (!$has) {
                    LegalCycle::create($data);
                    return redirect('legal/list-cycle')->withStatus(__('Cycle created successfully.'));
                } else {
                    return redirect('legal/list-cycle')->withStatus(__('Cycle already available.'));
                }
            } else {
                return redirect('legal/list-cycle')->withStatus(__('Cycle name not available.'));
            }
        }

        return view('legal.cycle.create_cycle');
    }

    public function toggleStatus(Request $request)
    {
        $user = Auth::user();

        // First, find the cycle ensuring it belongs to the logged-in client's scope
        $row = LegalCycle::where('id', $request->id)
            ->where('client_id', $user->client_id)
            ->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Cycle not found or not authorized.'], 403);
        }

        // Deactivate all cycles of the same client
        LegalCycle::where('client_id', $user->client_id)->update(['status' => 2]);

        // Activate/deactivate the requested cycle
        $row->status = $request->status;
        $row->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.'
        ]);
    }

    public function listCycle(Request $request)
    {
        // Fetch only the cycles where the client_id matches the logged-in user's ID
        $data = LegalCycle::where('client_id', Auth::user()->id)
            ->orderBy("id", "desc")
            ->get();

        return view('legal.cycle.cycle_list', compact('data'));
    }


    public function editCycle(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            if ($request->cycle_name) {
                $cycle = LegalCycle::find($id);
                if ($cycle) {
                    $existingCycle = LegalCycle::where('name', $request->cycle_name)->where('id', '!=', $id)->first();
                    if (!$existingCycle) {
                        $cycle->name = $request->cycle_name;
                        $cycle->created_by = Auth::user()->id;
                        $cycle->save();

                        return redirect('legal/list-cycle')->withStatus(__('Cycle updated successfully.'));
                    } else {
                        return redirect('legal/list-cycle')->withStatus(__('Cycle name already exists.'));
                    }
                } else {
                    return redirect('legal/list-cycle')->withStatus(__('Cycle not found.'));
                }
            } else {
                return redirect('legal/list-cycle')->withStatus(__('Cycle name not provided.'));
            }
        }

        $cycle = LegalCycle::find($id);
        if (!$cycle) {
            return redirect('legal/list-cycle')->withStatus(__('Cycle not found.'));
        }

        return view('legal.cycle.cycle_edit', compact('cycle'));
    }
}
