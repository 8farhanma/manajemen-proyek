<?php
namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Task;
use Illuminate\Http\Request;

class ChecklistItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isMember()) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Unauthorized. Only members can manage checklist items.'], 403);
                }
                abort(403, 'Unauthorized. Only members can manage checklist items.');
            }
            return $next($request);
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'task_id' => 'required|exists:tasks,id',
        ]);

        $checklistItem = ChecklistItem::create([
            'task_id' => $request->task_id,
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'data' =>  $checklistItem
            
        ]);
    }

    public function updateStatus(ChecklistItem $checklistItem)
    {
        $checklistItem->update([
            'completed' => !$checklistItem->completed === true ? 1 : 0, 
        ]);
        return response()->json([
            'success' => true,
            'status' => 200
        ]);
    }

    public function update(Request $request, ChecklistItem $checklistItem)
    {
        $checklistItem->update([
            'completed' => $request->has('completed'),
            'name' => $request->name,
        ]);
        return back()->with('success', 'Checklist item updated successfully.');
    }

    public function destroy(ChecklistItem $checklistItem)
    {
        $checklistItem->delete();
        return response()->json([
            'success' => true,
        ]);
    }
}
