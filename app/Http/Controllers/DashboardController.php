<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Course;
use App\Models\Workshop;
use App\Models\Testimonial;
use App\Models\Faculty;
use App\Models\Certificate;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function stats()
    {
        try {
            return response()->json([
                'blogs' => Blog::count(),
                'courses' => Course::count(),
                'workshops' => [
                    'total' => Workshop::count(),
                    'upcoming' => Workshop::where('status', 'upcoming')->orWhere('status', 'open')->count(),
                    'registrations' => Workshop::sum('registrations')
                ],
                'testimonials' => [
                    'total' => Testimonial::count(),
                    'featured' => Testimonial::where('is_featured', true)->count()
                ],
                'faculty' => Faculty::count(),
                'certificates' => Certificate::count(),
                'gallery' => Gallery::count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard stats error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching stats'], 500);
        }
    }
}
