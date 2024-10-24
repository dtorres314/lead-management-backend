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
        $searchQuery = $request->input('search', '');  // Get the search query if available
        $filterStatus = $request->input('status', '');  // Get the lead status filter

        // Generate a unique cache key based on the search query, page, and pagination size
        $cacheKey = "leads_page_{$page}_perPage_{$perPage}_search_{$searchQuery}_status_{$filterStatus}";

        // Try to get the data from Redis cache
        $leads = Cache::remember($cacheKey, 600, function () use ($searchQuery, $filterStatus, $perPage, $page) {
            $query = Lead::with('status');  // Eager load the lead status relationship

            // If there's a search query, apply the filter
            if (!empty($searchQuery)) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'like', '%' . $searchQuery . '%')
                      ->orWhere('email', 'like', '%' . $searchQuery . '%');
                });
            }

            // If there's a lead status filter, apply the filter
            if (!empty($filterStatus)) {
                $query->where('lead_status_id', $filterStatus);  // Filter by the status
            }

            return $query->paginate($perPage, ['*'], 'page', $page);
        });

        return response()->json($leads);
    }

    public function getStatuses()
    {
        // Get all lead statuses to populate the filter dropdown
        $statuses = \App\Models\LeadStatus::all();
        return response()->json($statuses);
    }
}
