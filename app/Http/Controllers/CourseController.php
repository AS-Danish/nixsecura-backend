<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::latest()->get();
        return response()->json($courses);
    }

    public function show($id)
    {
        $course = Course::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        return response()->json($course);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|string',
            'category' => 'required|string|max:255',
            'duration' => 'nullable|string|max:255',
            'curriculum' => 'nullable|array',
        ]);

        // Ensure curriculum is an array
        if (isset($validated['curriculum']) && !is_array($validated['curriculum'])) {
            $validated['curriculum'] = [];
        }

        $slug = Str::slug($validated['title']);
        $count = Course::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        $course = Course::create(array_merge($validated, ['slug' => $slug]));

        return response()->json($course, 201);
    }

    public function update(Request $request, string $id)
    {
        $course = Course::where('id', $id)->orWhere('slug', $id)->firstOrFail();

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image' => 'nullable|string',
            'category' => 'sometimes|required|string|max:255',
            'duration' => 'nullable|string|max:255',
            'curriculum' => 'nullable|array',
        ]);

        // Ensure curriculum is an array
        if (isset($validated['curriculum']) && !is_array($validated['curriculum'])) {
            $validated['curriculum'] = [];
        }

        if (isset($validated['title'])) {
            $slug = Str::slug($validated['title']);
            if ($slug !== $course->slug) {
                $count = Course::where('slug', $slug)->where('id', '!=', $course->id)->count();
                if ($count > 0) {
                    $slug = $slug . '-' . ($count + 1);
                }
                $validated['slug'] = $slug;
            }
        }

        $course->update($validated);

        return response()->json($course);
    }

    public function destroy(string $id)
    {
        $course = Course::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $course->delete();

        return response()->json(['message' => 'Course deleted successfully']);
    }
}
