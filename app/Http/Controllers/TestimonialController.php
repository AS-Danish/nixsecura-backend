<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::latest()->get();
        return response()->json($testimonials);
    }

    public function show($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        return response()->json($testimonial);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'testimonial' => 'nullable|string',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|string',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
        ]);

        // Ensure boolean is properly cast
        if (isset($validated['is_featured'])) {
            $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
        } else {
            $validated['is_featured'] = false;
        }

        $testimonial = Testimonial::create($validated);

        return response()->json($testimonial, 201);
    }

    public function update(Request $request, string $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'course' => 'nullable|string|max:255',
            'testimonial' => 'nullable|string',
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'image' => 'nullable|string',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
        ]);

        // Ensure boolean is properly cast
        if (isset($validated['is_featured'])) {
            $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
        }

        $testimonial->update($validated);

        return response()->json($testimonial);
    }

    public function destroy(string $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        return response()->json(['message' => 'Testimonial deleted successfully']);
    }
}
