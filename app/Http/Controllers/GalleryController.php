<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $gallery = Gallery::orderBy('order')->latest()->get();
        return response()->json($gallery);
    }

    public function show($id)
    {
        $gallery = Gallery::findOrFail($id);
        return response()->json($gallery);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'image' => 'required|string',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
        ]);

        // Ensure boolean is properly cast
        if (isset($validated['is_featured'])) {
            $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
        } else {
            $validated['is_featured'] = false;
        }

        $gallery = Gallery::create($validated);

        return response()->json($gallery, 201);
    }

    public function update(Request $request, string $id)
    {
        $gallery = Gallery::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:255',
            'image' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
        ]);

        // Ensure boolean is properly cast
        if (isset($validated['is_featured'])) {
            $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
        }

        $gallery->update($validated);

        return response()->json($gallery);
    }

    public function destroy(string $id)
    {
        $gallery = Gallery::findOrFail($id);
        $gallery->delete();

        return response()->json(['message' => 'Gallery item deleted successfully']);
    }
}
