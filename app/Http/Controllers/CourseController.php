<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Course::query();

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Category filtering
            if ($request->filled('category') && $request->input('category') !== 'All') {
                $query->where('category', $request->input('category'));
            }

            // Pagination (9 items per page to fit 3x3 grid)
            $courses = $query->latest()->paginate(9);
            
            return response()->json($courses);
        } catch (\Exception $e) {
            Log::error('Course index error: ' . $e->getMessage());
            // Return structure matching pagination even on error
            return response()->json(['data' => [], 'meta' => []], 200);
        }
    }

    public function show($id)
    {
        try {
            $course = Course::where('id', $id)->orWhere('slug', $id)->firstOrFail();
            return response()->json($course);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Course not found'], 404);
        } catch (\Exception $e) {
            Log::error('Course show error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching course'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|string|max:500', // Stores URL path, not base64
                'category' => 'required|string|max:255',
                'duration' => 'nullable|string|max:255',
                'curriculum' => 'nullable|array',
            ]);

            // Sanitize inputs
            $validated['title'] = strip_tags(trim($validated['title']));
            $validated['category'] = strip_tags(trim($validated['category']));
            if (isset($validated['duration'])) {
                $validated['duration'] = strip_tags(trim($validated['duration']));
            }

            // Ensure curriculum is an array and sanitize
            if (isset($validated['curriculum']) && !is_array($validated['curriculum'])) {
                $validated['curriculum'] = [];
            } else if (isset($validated['curriculum'])) {
                $validated['curriculum'] = array_values(array_map(function($item) {
                    return is_string($item) ? strip_tags(trim($item)) : $item;
                }, array_filter($validated['curriculum'], function($item) {
                    if (is_array($item)) return !empty($item);
                    return is_string($item) && !empty(trim($item));
                })));
            }

            $slug = Str::slug($validated['title']);
            $count = Course::where('slug', $slug)->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }

            $course = Course::create(array_merge($validated, ['slug' => $slug]));

            return response()->json($course, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Course store error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating course'], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $course = Course::where('id', $id)->orWhere('slug', $id)->firstOrFail();

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'image' => 'nullable|string|max:500', // Stores URL path, not base64
                'category' => 'sometimes|required|string|max:255',
                'duration' => 'nullable|string|max:255',
                'curriculum' => 'nullable|array',
            ]);

            // Sanitize inputs
            if (isset($validated['title'])) {
                $validated['title'] = strip_tags(trim($validated['title']));
            }
            if (isset($validated['category'])) {
                $validated['category'] = strip_tags(trim($validated['category']));
            }
            if (isset($validated['duration'])) {
                $validated['duration'] = strip_tags(trim($validated['duration']));
            }

            // Ensure curriculum is an array and sanitize
            if (isset($validated['curriculum']) && !is_array($validated['curriculum'])) {
                $validated['curriculum'] = [];
            } else if (isset($validated['curriculum'])) {
                $validated['curriculum'] = array_values(array_map(function($item) {
                    return is_string($item) ? strip_tags(trim($item)) : $item;
                }, array_filter($validated['curriculum'], function($item) {
                    if (is_array($item)) return !empty($item);
                    return is_string($item) && !empty(trim($item));
                })));
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Course not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Course update error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating course'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $course = Course::where('id', $id)->orWhere('slug', $id)->firstOrFail();
            $course->delete();

            return response()->json(['message' => 'Course deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Course not found'], 404);
        } catch (\Exception $e) {
            Log::error('Course destroy error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting course'], 500);
        }
    }
}
