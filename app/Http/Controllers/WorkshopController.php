<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkshopController extends Controller
{
    public function index()
    {
        $workshops = Workshop::latest()->get();
        return response()->json($workshops);
    }

    public function show($id)
    {
        $workshop = Workshop::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        return response()->json($workshop);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:0',
            'registrations' => 'nullable|integer|min:0',
            'status' => 'required|in:upcoming,open,completed,cancelled',
            'price' => 'nullable|numeric|min:0',
            'instructors' => 'nullable|array',
        ]);

        // Ensure arrays are properly formatted
        if (isset($validated['instructors']) && !is_array($validated['instructors'])) {
            $validated['instructors'] = [];
        }
        // Set default registrations if not provided
        if (!isset($validated['registrations'])) {
            $validated['registrations'] = 0;
        }

        $slug = Str::slug($validated['title']);
        $count = Workshop::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        $workshop = Workshop::create(array_merge($validated, ['slug' => $slug]));

        return response()->json($workshop, 201);
    }

    public function update(Request $request, string $id)
    {
        $workshop = Workshop::where('id', $id)->orWhere('slug', $id)->firstOrFail();

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'date' => 'sometimes|required|date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:0',
            'registrations' => 'nullable|integer|min:0',
            'status' => 'sometimes|required|in:upcoming,open,completed,cancelled',
            'price' => 'nullable|numeric|min:0',
            'instructors' => 'nullable|array',
        ]);

        // Ensure arrays are properly formatted
        if (isset($validated['instructors']) && !is_array($validated['instructors'])) {
            $validated['instructors'] = [];
        }

        if (isset($validated['title'])) {
            $slug = Str::slug($validated['title']);
            if ($slug !== $workshop->slug) {
                $count = Workshop::where('slug', $slug)->where('id', '!=', $workshop->id)->count();
                if ($count > 0) {
                    $slug = $slug . '-' . ($count + 1);
                }
                $validated['slug'] = $slug;
            }
        }

        $workshop->update($validated);

        return response()->json($workshop);
    }

    public function destroy(string $id)
    {
        $workshop = Workshop::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $workshop->delete();

        return response()->json(['message' => 'Workshop deleted successfully']);
    }
}
