<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index()
    {
        // Get all faculty, including inactive ones for admin dashboard
        $faculty = Faculty::orderBy('order')->orderBy('created_at', 'desc')->get();
        return response()->json($faculty);
    }

    public function show($id)
    {
        $faculty = Faculty::findOrFail($id);
        return response()->json($faculty);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'experience' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'qualifications' => 'nullable|array',
            'expertise_areas' => 'nullable|array',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Ensure arrays are properly formatted
        if (isset($validated['qualifications']) && !is_array($validated['qualifications'])) {
            $validated['qualifications'] = [];
        }
        if (isset($validated['expertise_areas']) && !is_array($validated['expertise_areas'])) {
            $validated['expertise_areas'] = [];
        }
        // Ensure boolean is properly cast
        if (isset($validated['is_active'])) {
            $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
        } else {
            $validated['is_active'] = true; // Default to active
        }

        $faculty = Faculty::create($validated);

        return response()->json($faculty, 201);
    }

    public function update(Request $request, string $id)
    {
        $faculty = Faculty::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'specialization' => 'sometimes|required|string|max:255',
            'bio' => 'nullable|string',
            'experience' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'qualifications' => 'nullable|array',
            'expertise_areas' => 'nullable|array',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Ensure arrays are properly formatted
        if (isset($validated['qualifications']) && !is_array($validated['qualifications'])) {
            $validated['qualifications'] = [];
        }
        if (isset($validated['expertise_areas']) && !is_array($validated['expertise_areas'])) {
            $validated['expertise_areas'] = [];
        }
        // Ensure boolean is properly cast
        if (isset($validated['is_active'])) {
            $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
        }

        $faculty->update($validated);

        return response()->json($faculty);
    }

    public function destroy(string $id)
    {
        $faculty = Faculty::findOrFail($id);
        $faculty->delete();

        return response()->json(['message' => 'Faculty member deleted successfully']);
    }
}
