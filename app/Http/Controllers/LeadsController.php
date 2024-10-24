<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LeadsController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);  // Default to page 1
        $perPage = $request->input('perPage', 10);  // Default to 10 records per page

        // Generate a cache key based on the page number and pagination size
        $cacheKey = "leads_page_{$page}_perPage_{$perPage}";

        // Try to get the data from Redis cache
        $leads = Cache::remember($cacheKey, 600, function () use ($perPage, $page) {
            return Lead::with('status')
                ->paginate($perPage, ['*'], 'page', $page);
        });

        return response()->json($leads);
    }
}
