<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    public function index()
    {
        try {
            $certificates = Certificate::orderBy('order')->latest()->get();
            return response()->json($certificates->toArray());
        } catch (\Exception $e) {
            Log::error('Certificate index error: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }

    public function show($id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            return response()->json($certificate);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Certificate not found'], 404);
        } catch (\Exception $e) {
            Log::error('Certificate show error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching certificate'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'issuer' => 'required|string|max:255',
                'year' => 'required|string|max:10',
                'image' => 'nullable|string|max:500', // Stores URL path, not base64
                'description' => 'nullable|string',
                'certificate_number' => 'nullable|string|max:255',
                'issue_date' => 'nullable|date',
                'expiry_date' => 'nullable|date',
                'order' => 'nullable|integer|min:0',
                'is_featured' => 'nullable|boolean',
            ]);

            // Sanitize inputs
            $validated['title'] = strip_tags(trim($validated['title']));
            $validated['issuer'] = strip_tags(trim($validated['issuer']));
            if (isset($validated['description'])) {
                $validated['description'] = strip_tags(trim($validated['description']));
            }
            if (isset($validated['certificate_number'])) {
                $validated['certificate_number'] = strip_tags(trim($validated['certificate_number']));
            }

            // Ensure boolean is properly cast
            if (isset($validated['is_featured'])) {
                $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
            } else {
                $validated['is_featured'] = false;
            }

            // Set default order if not provided
            if (!isset($validated['order'])) {
                $maxOrder = Certificate::max('order') ?? 0;
                $validated['order'] = $maxOrder + 1;
            }

            $certificate = Certificate::create($validated);

            return response()->json($certificate, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Certificate store error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating certificate'], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $certificate = Certificate::findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'issuer' => 'sometimes|required|string|max:255',
                'year' => 'sometimes|required|string|max:10',
                'image' => 'nullable|string|max:500', // Stores URL path, not base64
                'description' => 'nullable|string',
                'certificate_number' => 'nullable|string|max:255',
                'issue_date' => 'nullable|date',
                'expiry_date' => 'nullable|date',
                'order' => 'nullable|integer|min:0',
                'is_featured' => 'nullable|boolean',
            ]);

            // Sanitize inputs
            if (isset($validated['title'])) {
                $validated['title'] = strip_tags(trim($validated['title']));
            }
            if (isset($validated['issuer'])) {
                $validated['issuer'] = strip_tags(trim($validated['issuer']));
            }
            if (isset($validated['description'])) {
                $validated['description'] = strip_tags(trim($validated['description']));
            }
            if (isset($validated['certificate_number'])) {
                $validated['certificate_number'] = strip_tags(trim($validated['certificate_number']));
            }

            // Ensure boolean is properly cast
            if (isset($validated['is_featured'])) {
                $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
            }

            $certificate->update($validated);

            return response()->json($certificate);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Certificate not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Certificate update error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating certificate'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            $certificate->delete();

            return response()->json(['message' => 'Certificate deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Certificate not found'], 404);
        } catch (\Exception $e) {
            Log::error('Certificate destroy error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting certificate'], 500);
        }
    }
}
