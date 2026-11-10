<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,achieved',
        ]);

        $milestone = $project->milestones()->create($validated);

        ActivityLog::log('Created Milestone', "Created milestone: {$milestone->title} on project [{$project->code}]", $project->id);

        return back()->with('success', 'Milestone added successfully!');
    }

    public function update(Request $request, Milestone $milestone)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,achieved',
        ]);

        $milestone->update($validated);

        ActivityLog::log('Updated Milestone', "Updated milestone: {$milestone->title}", $milestone->project_id);

        return back()->with('success', 'Milestone updated successfully!');
    }

    public function toggleStatus(Milestone $milestone)
    {
        $milestone->status = ($milestone->status === 'achieved') ? 'in_progress' : 'achieved';
        $milestone->save();

        ActivityLog::log('Toggled Milestone', "Milestone '{$milestone->title}' status set to {$milestone->status}", $milestone->project_id);

        return back()->with('success', "Milestone marked as {$milestone->status}!");
    }

    public function destroy(Milestone $milestone)
    {
        $title = $milestone->title;
        $projectId = $milestone->project_id;
        $milestone->delete();

        ActivityLog::log('Deleted Milestone', "Deleted milestone: {$title}", $projectId);

        return back()->with('success', 'Milestone deleted successfully!');
    }
}
