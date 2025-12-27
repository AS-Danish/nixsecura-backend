<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = Blog::latest()->get();
        return response()->json($blogs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|string', // Assuming URL for now
            'category' => 'required|string',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
        ]);

        $slug = Str::slug($validated['title']);
        $count = Blog::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        $blog = Blog::create(array_merge($validated, ['slug' => $slug]));

        return response()->json($blog, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Try to find by ID or Slug
        $blog = Blog::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        
        return response()->json($blog);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $blog = Blog::where('id', $id)->orWhere('slug', $id)->firstOrFail();

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'excerpt' => 'sometimes|required|string',
            'content' => 'sometimes|required|string',
            'image' => 'nullable|string',
            'category' => 'sometimes|required|string',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
        ]);

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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blog = Blog::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully']);
    }
}
