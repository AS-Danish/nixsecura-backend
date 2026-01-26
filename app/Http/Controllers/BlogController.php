<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $blogs = Blog::latest()->get();
            return response()->json($blogs->toArray());
        } catch (\Exception $e) {
            Log::error('Blog index error: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'excerpt' => 'required|string',
                'content' => 'required|string',
                'image' => 'nullable|string|max:5000',
                'category' => 'required|string|max:255',
                'published_at' => 'nullable|date',
                'tags' => 'nullable|array',
            ]);

            // Sanitize inputs
            $validated['title'] = strip_tags(trim($validated['title']));
            $validated['excerpt'] = strip_tags(trim($validated['excerpt']));
            $validated['category'] = strip_tags(trim($validated['category']));

            // Ensure tags is an array and sanitize
            if (isset($validated['tags']) && !is_array($validated['tags'])) {
                $validated['tags'] = [];
            } else if (isset($validated['tags'])) {
                $validated['tags'] = array_map(function($tag) {
                    return strip_tags(trim($tag));
                }, array_filter($validated['tags'], function($tag) {
                    return !empty(trim($tag));
                }));
            }

            $slug = Str::slug($validated['title']);
            $count = Blog::where('slug', $slug)->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }

            $blog = Blog::create(array_merge($validated, ['slug' => $slug]));

            return response()->json($blog, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Blog store error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating blog'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Try to find by ID or Slug
            $blog = Blog::where('id', $id)->orWhere('slug', $id)->firstOrFail();
            return response()->json($blog);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Blog not found'], 404);
        } catch (\Exception $e) {
            Log::error('Blog show error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching blog'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $blog = Blog::where('id', $id)->orWhere('slug', $id)->firstOrFail();

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'excerpt' => 'sometimes|required|string',
                'content' => 'sometimes|required|string',
                'image' => 'nullable|string|max:5000',
                'category' => 'sometimes|required|string|max:255',
                'published_at' => 'nullable|date',
                'tags' => 'nullable|array',
            ]);

            // Sanitize inputs
            if (isset($validated['title'])) {
                $validated['title'] = strip_tags(trim($validated['title']));
            }
            if (isset($validated['excerpt'])) {
                $validated['excerpt'] = strip_tags(trim($validated['excerpt']));
            }
            if (isset($validated['category'])) {
                $validated['category'] = strip_tags(trim($validated['category']));
            }

            // Ensure tags is an array and sanitize
            if (isset($validated['tags']) && !is_array($validated['tags'])) {
                $validated['tags'] = [];
            } else if (isset($validated['tags'])) {
                $validated['tags'] = array_map(function($tag) {
                    return strip_tags(trim($tag));
                }, array_filter($validated['tags'], function($tag) {
                    return !empty(trim($tag));
                }));
            }

            if (isset($validated['title'])) {
                 $slug = Str::slug($validated['title']);
                 if ($slug !== $blog->slug) {
                     $count = Blog::where('slug', $slug)->where('id', '!=', $blog->id)->count();
                     if ($count > 0) {
                         $slug = $slug . '-' . ($count + 1);
                     }
                     $validated['slug'] = $slug;
                 }
            }

            $blog->update($validated);

            return response()->json($blog);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Blog not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Blog update error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating blog'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $blog = Blog::where('id', $id)->orWhere('slug', $id)->firstOrFail();
            $blog->delete();

            return response()->json(['message' => 'Blog deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Blog not found'], 404);
        } catch (\Exception $e) {
            Log::error('Blog destroy error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting blog'], 500);
        }
    }
}
