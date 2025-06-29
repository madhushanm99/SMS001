<?php

namespace App\Http\Controllers;

use App\Models\JobTypes;
use Illuminate\Http\Request;

class JobTypeController extends Controller
{
    public function index(Request $request)
    {
        // Build query for filtering
        $query = JobTypes::query();

        // Search filter
        if ($search = $request->input('search')) {
            $query->where('jobType', 'like', "%$search%");
        }

        $jobtypes = $query->latest()->paginate(10)->withQueryString();

        // If the request is AJAX, return the view partial with filtered results
        if ($request->ajax()) {
            return view('jobtypes.table', compact('jobtypes'))->render();
        }

        return view('jobtypes.index', compact('jobtypes'));
    }

    public function create()
    {
        return view('jobtypes.create');
    }

    public function store(Request $request)
    {
        // Validate input data
        $request->validate([
            'jobType' => 'required|string|max:255|unique:job_types,jobType',
            'salesPrice' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ]);

        // Create the job type
        JobTypes::create([
            'jobCustomID' => JobTypes::generateJobCustomID(), // Generate custom job ID
            'jobType' => $request->jobType,
            'salesPrice' => $request->salesPrice,
            'status' => $request->status,
        ]);

        return redirect()->route('jobtypes.index')->with('success', 'Job Type created successfully.');
    }

    public function show(JobTypes $jobtype)
    {
        return view('jobtypes.show', compact('jobtype'));
    }

    public function edit(JobTypes $jobtype)
    {
        return view('jobtypes.edit', compact('jobtype'));
    }

    public function update(Request $request, JobTypes $jobtype)
    {
        // Validate input data
        $request->validate([
            'jobType' => 'required|string|max:255|unique:job_types,jobType,' . $jobtype->id,
            'salesPrice' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ]);

        // Update the job type details
        $jobtype->update([
            'jobType' => $request->jobType,
            'salesPrice' => $request->salesPrice,
            'status' => $request->status,
        ]);

        return redirect()->route('jobtypes.index')->with('success', 'Job Type updated successfully.');
    }

    public function destroy(JobTypes $jobtype)
    {
        // Soft delete the job type by updating the status
        $jobtype->update(['status' => false]);

        return back()->with('success', 'Job Type marked as inactive.');
    }
}
