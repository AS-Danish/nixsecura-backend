<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class WorkshopController extends Controller
{
    public function index()
    {
        try {
            $workshops = Workshop::latest()->get();
            return response()->json($workshops->toArray());
        } catch (\Exception $e) {
            Log::error('Workshop index error: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }

    public function show($id)
    {
        try {
            $workshop = Workshop::where('id', $id)->orWhere('slug', $id)->firstOrFail();
            return response()->json($workshop);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Workshop not found'], 404);
        } catch (\Exception $e) {
            Log::error('Workshop show error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching workshop'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|string|max:5000',
                'date' => 'required|date',
                'start_time' => 'nullable|string|max:50',
                'end_time' => 'nullable|string|max:50',
                'location' => 'nullable|string|max:255',
                'max_participants' => 'nullable|integer|min:0',
                'registrations' => 'nullable|integer|min:0',
                'status' => 'required|in:upcoming,open,completed,cancelled',
                'price' => 'nullable|numeric|min:0',
                'instructors' => 'nullable|array',
            ]);

            // Sanitize inputs
            $validated['title'] = strip_tags(trim($validated['title']));
            if (isset($validated['description'])) {
                $validated['description'] = strip_tags(trim($validated['description']));
            }
            if (isset($validated['location'])) {
                $validated['location'] = strip_tags(trim($validated['location']));
            }

            // Ensure arrays are properly formatted and sanitized
            if (isset($validated['instructors']) && !is_array($validated['instructors'])) {
                $validated['instructors'] = [];
            } else if (isset($validated['instructors'])) {
                $validated['instructors'] = array_map(function($instructor) {
                    return strip_tags(trim($instructor));
                }, array_filter($validated['instructors'], function($instructor) {
                    return !empty(trim($instructor));
                }));
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Workshop store error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating workshop'], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $workshop = Workshop::where('id', $id)->orWhere('slug', $id)->firstOrFail();

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|string|max:5000',
                'date' => 'sometimes|required|date',
                'start_time' => 'nullable|string|max:50',
                'end_time' => 'nullable|string|max:50',
                'location' => 'nullable|string|max:255',
                'max_participants' => 'nullable|integer|min:0',
                'registrations' => 'nullable|integer|min:0',
                'status' => 'sometimes|required|in:upcoming,open,completed,cancelled',
                'price' => 'nullable|numeric|min:0',
                'instructors' => 'nullable|array',
            ]);

            // Sanitize inputs
            if (isset($validated['title'])) {
                $validated['title'] = strip_tags(trim($validated['title']));
            }
            if (isset($validated['description'])) {
                $validated['description'] = strip_tags(trim($validated['description']));
            }
            if (isset($validated['location'])) {
                $validated['location'] = strip_tags(trim($validated['location']));
            }

            // Ensure arrays are properly formatted and sanitized
            if (isset($validated['instructors']) && !is_array($validated['instructors'])) {
                $validated['instructors'] = [];
            } else if (isset($validated['instructors'])) {
                $validated['instructors'] = array_map(function($instructor) {
                    return strip_tags(trim($instructor));
                }, array_filter($validated['instructors'], function($instructor) {
                    return !empty(trim($instructor));
                }));
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Workshop not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Workshop update error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating workshop'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $workshop = Workshop::where('id', $id)->orWhere('slug', $id)->firstOrFail();
            $workshop->delete();

            return response()->json(['message' => 'Workshop deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Workshop not found'], 404);
        } catch (\Exception $e) {
            Log::error('Workshop destroy error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting workshop'], 500);
        }
    }
}
