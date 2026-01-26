<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ImageUploadController extends Controller
{
    /**
     * Upload an image file and return its URL
     */
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240', // 10MB max
            ]);

            $file = $request->file('image');
            
            // Generate unique filename
            $filename = Str::random(40) . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Ensure images directory exists
            if (!Storage::disk('public')->exists('images')) {
                Storage::disk('public')->makeDirectory('images');
            }
            
            // Store in public/images directory
            $path = $file->storeAs('images', $filename, 'public');
            
            // Return the public URL - ensure it's absolute
            $url = Storage::disk('public')->url($path);
            
            // Make sure URL is absolute (prepend base URL if relative)
            if (!\Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])) {
                $baseUrl = config('app.url', 'http://localhost:8000');
                $url = rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
            }
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Image upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an image file
     */
    public function delete(Request $request)
    {
        try {
            $request->validate([
                'path' => 'required|string',
            ]);

            $path = $request->input('path');
            
            // Remove 'images/' prefix if present in path
            if (strpos($path, 'images/') === 0) {
                $path = $path;
            } elseif (strpos($path, '/storage/images/') !== false) {
                $path = 'images/' . basename($path);
            } else {
                $path = 'images/' . basename($path);
            }

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                return response()->json([
                    'success' => true,
                    'message' => 'Image deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Image delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image'
            ], 500);
        }
    }
}
