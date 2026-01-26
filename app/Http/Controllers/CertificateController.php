<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::orderBy('order')->latest()->get();
        return response()->json($certificates);
    }

    public function show($id)
    {
        $certificate = Certificate::findOrFail($id);
        return response()->json($certificate);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'year' => 'required|string|max:10',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'certificate_number' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
        ]);

        // Ensure boolean is properly cast
        if (isset($validated['is_featured'])) {
            $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
        } else {
            $validated['is_featured'] = false;
        }

        $certificate = Certificate::create($validated);

        return response()->json($certificate, 201);
    }

    public function update(Request $request, string $id)
    {
        $certificate = Certificate::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'issuer' => 'sometimes|required|string|max:255',
            'year' => 'sometimes|required|string|max:10',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'certificate_number' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
        ]);

        // Ensure boolean is properly cast
        if (isset($validated['is_featured'])) {
            $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
        }

        $certificate->update($validated);

        return response()->json($certificate);
    }

    public function destroy(string $id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->delete();

        return response()->json(['message' => 'Certificate deleted successfully']);
    }
}
