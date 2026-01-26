<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GalleryController extends Controller
{
    public function index()
    {
        try {
            $gallery = Gallery::orderBy('order')->latest()->get();
            return response()->json($gallery->toArray());
        } catch (\Exception $e) {
            Log::error('Gallery index error: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }

    public function show($id)
    {
        try {
            $gallery = Gallery::findOrFail($id);
            return response()->json($gallery);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Gallery item not found'], 404);
        } catch (\Exception $e) {
            Log::error('Gallery show error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching gallery item'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'image' => 'required|string|max:500', // Stores URL path, not base64
                'description' => 'nullable|string',
                'order' => 'nullable|integer|min:0',
                'is_featured' => 'nullable|boolean',
            ]);

            // Sanitize inputs
            $validated['title'] = strip_tags(trim($validated['title']));
            $validated['category'] = strip_tags(trim($validated['category']));
            if (isset($validated['description'])) {
                $validated['description'] = strip_tags(trim($validated['description']));
            }

            // Ensure boolean is properly cast
            if (isset($validated['is_featured'])) {
                $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
            } else {
                $validated['is_featured'] = false;
            }

            // Set default order if not provided
            if (!isset($validated['order'])) {
                $maxOrder = Gallery::max('order') ?? 0;
                $validated['order'] = $maxOrder + 1;
            }

            $gallery = Gallery::create($validated);

            return response()->json($gallery, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Gallery store error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating gallery item'], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $gallery = Gallery::findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'category' => 'sometimes|required|string|max:255',
                'image' => 'sometimes|required|string|max:500', // Stores URL path, not base64
                'description' => 'nullable|string',
                'order' => 'nullable|integer|min:0',
                'is_featured' => 'nullable|boolean',
            ]);

            // Sanitize inputs
            if (isset($validated['title'])) {
                $validated['title'] = strip_tags(trim($validated['title']));
            }
            if (isset($validated['category'])) {
                $validated['category'] = strip_tags(trim($validated['category']));
            }
            if (isset($validated['description'])) {
                $validated['description'] = strip_tags(trim($validated['description']));
            }

            // Ensure boolean is properly cast
            if (isset($validated['is_featured'])) {
                $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
            }

            $gallery->update($validated);

            return response()->json($gallery);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Gallery item not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Gallery update error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating gallery item'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $gallery = Gallery::findOrFail($id);
            $gallery->delete();

            return response()->json(['message' => 'Gallery item deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Gallery item not found'], 404);
        } catch (\Exception $e) {
            Log::error('Gallery destroy error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting gallery item'], 500);
        }
    }
}
