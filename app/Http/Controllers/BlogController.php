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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Blog::latest();
            
            // Limit fields to reduce payload size (exclude full content)
            $query->select('id', 'title', 'slug', 'excerpt', 'image', 'category', 'published_at', 'author_name', 'author_image', 'created_at', 'read_time');

            if ($request->has('limit')) {
                $query->limit($request->input('limit'));
            }

            $blogs = $query->get();
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
                'image' => 'nullable|string|max:500', // Now stores URL path, not base64
                'category' => 'required|string|max:255',
                'published_at' => 'nullable|date',
                'tags' => 'nullable|array',
            ]);

            // Sanitize inputs (but preserve HTML in content for rich text editor)
            $validated['title'] = strip_tags(trim($validated['title']));
            $validated['excerpt'] = strip_tags(trim($validated['excerpt']));
            $validated['category'] = strip_tags(trim($validated['category']));
            // Content should preserve HTML from rich text editor, just trim whitespace
            if (isset($validated['content'])) {
                $validated['content'] = trim($validated['content']);
                // Check if content is empty (just HTML tags with no text)
                $textContent = strip_tags($validated['content']);
                if (trim($textContent) === '') {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => ['content' => ['Content cannot be empty.']]
                    ], 422);
                }
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

            $slug = Str::slug($validated['title']);
            $count = Blog::where('slug', $slug)->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }

            // Dashboard doesn't provide author fields; set safe defaults
            $user = $request->user();
            $blogPayload = array_merge($validated, [
                'slug' => $slug,
                'author_name' => $user && $user->name ? $user->name : 'Admin',
                'author_role' => $user ? 'Administrator' : 'Admin',
                'author_image' => null,
            ]);

            // Optional: simple read_time estimate (e.g. "5 min read")
            $plainText = trim(preg_replace('/\s+/', ' ', strip_tags($validated['content'] ?? '')));
            if ($plainText !== '') {
                // Count words more reliably (handles unicode and special characters)
                $words = count(preg_split('/\s+/u', $plainText, -1, PREG_SPLIT_NO_EMPTY));
                $minutes = max(1, (int) ceil($words / 200));
                $blogPayload['read_time'] = $minutes . ' min read';
            } else {
                $blogPayload['read_time'] = '1 min read';
            }

            // Ensure all required fields are present
            if (empty($blogPayload['author_name'])) {
                $blogPayload['author_name'] = 'Admin';
            }
            if (empty($blogPayload['author_role'])) {
                $blogPayload['author_role'] = 'Admin';
            }

            $blog = Blog::create($blogPayload);

            return response()->json($blog, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Blog store error: ' . $e->getMessage());
            Log::error('Blog store error trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'An error occurred while creating blog',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
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
                'image' => 'nullable|string|max:500', // Now stores URL path, not base64
                'category' => 'sometimes|required|string|max:255',
                'published_at' => 'nullable|date',
                'tags' => 'nullable|array',
            ]);

            // Sanitize inputs (but preserve HTML in content for rich text editor)
            if (isset($validated['title'])) {
                $validated['title'] = strip_tags(trim($validated['title']));
            }
            if (isset($validated['excerpt'])) {
                $validated['excerpt'] = strip_tags(trim($validated['excerpt']));
            }
            if (isset($validated['category'])) {
                $validated['category'] = strip_tags(trim($validated['category']));
            }
            // Content should preserve HTML from rich text editor, just trim whitespace
            if (isset($validated['content'])) {
                $validated['content'] = trim($validated['content']);
                // Check if content is empty (just HTML tags with no text)
                $textContent = strip_tags($validated['content']);
                if (trim($textContent) === '') {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => ['content' => ['Content cannot be empty.']]
                    ], 422);
                }
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

            // Keep read_time roughly in sync if content changes
            if (isset($validated['content'])) {
                $plainText = trim(preg_replace('/\s+/', ' ', strip_tags($validated['content'])));
                if ($plainText !== '') {
                    // Count words more reliably (handles unicode and special characters)
                    $words = count(preg_split('/\s+/u', $plainText, -1, PREG_SPLIT_NO_EMPTY));
                    $minutes = max(1, (int) ceil($words / 200));
                    $validated['read_time'] = $minutes . ' min read';
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
