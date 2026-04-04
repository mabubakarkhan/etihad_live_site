<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = JobApplication::with('career')->orderByDesc('id');
        if ($request->filled('career_id')) {
            $query->where('career_id', $request->career_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $applications = $query->limit(500)->get();
        $careers = \App\Models\Career::orderBy('title')->get(['id', 'title']);
        return view('admin.job_applications.index', compact('applications', 'careers'));
    }

    public function show(JobApplication $jobApplication)
    {
        $jobApplication->load('career');
        return view('admin.job_applications.show', compact('jobApplication'));
    }

    public function updateStatus(Request $request, JobApplication $jobApplication)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,seen,accept,considering,rejected'],
        ]);
        $jobApplication->update($validated);
        return redirect()->route('admin.job-applications.show', $jobApplication)->with('status', 'Status updated.');
    }
}
