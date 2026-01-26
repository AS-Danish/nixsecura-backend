<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FacultyController extends Controller
{
    public function index()
    {
        try {
            // Get all faculty, including inactive ones for admin dashboard
            $faculty = Faculty::orderBy('order')->orderBy('created_at', 'desc')->get();
            // Always return an array, even if empty
            return response()->json($faculty->toArray());
        } catch (\Exception $e) {
            Log::error('Faculty index error: ' . $e->getMessage());
            // Return empty array instead of error to prevent frontend issues
            return response()->json([], 200);
        }
    }

    public function show($id)
    {
        try {
            $faculty = Faculty::findOrFail($id);
            return response()->json($faculty);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Faculty not found'], 404);
        } catch (\Exception $e) {
            Log::error('Faculty show error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching faculty'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'specialization' => 'required|string|max:255',
                'bio' => 'nullable|string',
                'experience' => 'nullable|string|max:255',
                'image' => 'nullable|string|max:500', // Stores URL path, not base64
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'qualifications' => 'nullable|array',
                'expertise_areas' => 'nullable|array',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            // Sanitize string inputs
            $validated['name'] = strip_tags(trim($validated['name']));
            $validated['specialization'] = strip_tags(trim($validated['specialization']));
            if (isset($validated['bio'])) {
                $validated['bio'] = strip_tags(trim($validated['bio']));
            }
            if (isset($validated['experience'])) {
                $validated['experience'] = strip_tags(trim($validated['experience']));
            }
            if (isset($validated['email'])) {
                $validated['email'] = filter_var(trim($validated['email']), FILTER_SANITIZE_EMAIL);
            }
            if (isset($validated['phone'])) {
                $validated['phone'] = preg_replace('/[^0-9+\-() ]/', '', trim($validated['phone']));
            }

            // Ensure arrays are properly formatted and sanitized
            if (isset($validated['qualifications']) && !is_array($validated['qualifications'])) {
                $validated['qualifications'] = [];
            } else if (isset($validated['qualifications'])) {
                $validated['qualifications'] = array_map(function($q) {
                    return strip_tags(trim($q));
                }, array_filter($validated['qualifications'], function($q) {
                    return !empty(trim($q));
                }));
            }
            
            if (isset($validated['expertise_areas']) && !is_array($validated['expertise_areas'])) {
                $validated['expertise_areas'] = [];
            } else if (isset($validated['expertise_areas'])) {
                $validated['expertise_areas'] = array_map(function($e) {
                    return strip_tags(trim($e));
                }, array_filter($validated['expertise_areas'], function($e) {
                    return !empty(trim($e));
                }));
            }
            
            // Ensure boolean is properly cast
            if (isset($validated['is_active'])) {
                $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
            } else {
                $validated['is_active'] = true; // Default to active
            }

            // Ensure order is set
            if (!isset($validated['order'])) {
                $maxOrder = Faculty::max('order') ?? 0;
                $validated['order'] = $maxOrder + 1;
            }

            $faculty = Faculty::create($validated);

            return response()->json($faculty, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Faculty store error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating faculty'], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $faculty = Faculty::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'specialization' => 'sometimes|required|string|max:255',
                'bio' => 'nullable|string',
                'experience' => 'nullable|string|max:255',
                'image' => 'nullable|string|max:500', // Stores URL path, not base64
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'qualifications' => 'nullable|array',
                'expertise_areas' => 'nullable|array',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            // Sanitize string inputs
            if (isset($validated['name'])) {
                $validated['name'] = strip_tags(trim($validated['name']));
            }
            if (isset($validated['specialization'])) {
                $validated['specialization'] = strip_tags(trim($validated['specialization']));
            }
            if (isset($validated['bio'])) {
                $validated['bio'] = strip_tags(trim($validated['bio']));
            }
            if (isset($validated['experience'])) {
                $validated['experience'] = strip_tags(trim($validated['experience']));
            }
            if (isset($validated['email'])) {
                $validated['email'] = filter_var(trim($validated['email']), FILTER_SANITIZE_EMAIL);
            }
            if (isset($validated['phone'])) {
                $validated['phone'] = preg_replace('/[^0-9+\-() ]/', '', trim($validated['phone']));
            }

            // Ensure arrays are properly formatted and sanitized
            if (isset($validated['qualifications']) && !is_array($validated['qualifications'])) {
                $validated['qualifications'] = [];
            } else if (isset($validated['qualifications'])) {
                $validated['qualifications'] = array_map(function($q) {
                    return strip_tags(trim($q));
                }, array_filter($validated['qualifications'], function($q) {
                    return !empty(trim($q));
                }));
            }
            
            if (isset($validated['expertise_areas']) && !is_array($validated['expertise_areas'])) {
                $validated['expertise_areas'] = [];
            } else if (isset($validated['expertise_areas'])) {
                $validated['expertise_areas'] = array_map(function($e) {
                    return strip_tags(trim($e));
                }, array_filter($validated['expertise_areas'], function($e) {
                    return !empty(trim($e));
                }));
            }
            
            // Ensure boolean is properly cast
            if (isset($validated['is_active'])) {
                $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
            }

            $faculty->update($validated);

            return response()->json($faculty);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Faculty not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Faculty update error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating faculty'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $faculty = Faculty::findOrFail($id);
            $faculty->delete();

            return response()->json(['message' => 'Faculty member deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Faculty not found'], 404);
        } catch (\Exception $e) {
            Log::error('Faculty destroy error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting faculty'], 500);
        }
    }
}
