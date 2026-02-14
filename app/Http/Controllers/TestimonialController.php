<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestimonialController extends Controller
{
    public function index()
    {
        try {
            $testimonials = Testimonial::latest()->get();
            return response()->json($testimonials->toArray());
        } catch (\Exception $e) {
            Log::error('Testimonial index error: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }

    public function show($id)
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            return response()->json($testimonial);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Testimonial not found'], 404);
        } catch (\Exception $e) {
            Log::error('Testimonial show error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching testimonial'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'course' => 'nullable|string|max:255',
                'testimonial' => 'nullable|string',
                'rating' => 'nullable|integer|min:1|max:5',
                'image' => 'nullable|string|max:500', // Stores URL path, not base64
                'position' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'is_featured' => 'nullable|boolean',
            ]);

            // Sanitize inputs
            $validated['name'] = strip_tags(trim($validated['name']));
            if (isset($validated['course'])) {
                $validated['course'] = strip_tags(trim($validated['course']));
            }
            if (isset($validated['testimonial'])) {
                $validated['testimonial'] = strip_tags(trim($validated['testimonial']));
            }
            if (isset($validated['position'])) {
                $validated['position'] = strip_tags(trim($validated['position']));
            }
            if (isset($validated['company'])) {
                $validated['company'] = strip_tags(trim($validated['company']));
            }

            // Ensure boolean is properly cast
            if (isset($validated['is_featured'])) {
                $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
            } else {
                $validated['is_featured'] = false;
            }

            $testimonial = Testimonial::create($validated);

            return response()->json($testimonial, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Testimonial store error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating testimonial'], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $testimonial = Testimonial::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'course' => 'nullable|string|max:255',
                'testimonial' => 'nullable|string',
                'rating' => 'nullable|integer|min:1|max:5',
                'image' => 'nullable|string|max:500', // Stores URL path, not base64
                'position' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'is_featured' => 'nullable|boolean',
            ]);

            // Sanitize inputs
            if (isset($validated['name'])) {
                $validated['name'] = strip_tags(trim($validated['name']));
            }
            if (isset($validated['course'])) {
                $validated['course'] = strip_tags(trim($validated['course']));
            }
            if (isset($validated['testimonial'])) {
                $validated['testimonial'] = strip_tags(trim($validated['testimonial']));
            }
            if (isset($validated['position'])) {
                $validated['position'] = strip_tags(trim($validated['position']));
            }
            if (isset($validated['company'])) {
                $validated['company'] = strip_tags(trim($validated['company']));
            }

            // Ensure boolean is properly cast
            if (isset($validated['is_featured'])) {
                $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
            }

            $testimonial->update($validated);

            return response()->json($testimonial);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Testimonial not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Testimonial update error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating testimonial'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            $testimonial->delete();

            return response()->json(['message' => 'Testimonial deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Testimonial not found'], 404);
        } catch (\Exception $e) {
            Log::error('Testimonial destroy error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting testimonial'], 500);
        }
    }
}
