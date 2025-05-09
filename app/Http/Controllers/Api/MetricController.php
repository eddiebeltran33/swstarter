<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Metric;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MetricController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $day = Carbon::parse($request->query('day'));
        $startOfDay = $day->copy()->startOfDay();
        $endOfDay = $day->copy()->endOfDay();



        return Metric::whereBetween('start_at', [$startOfDay, $endOfDay])
            ->orderBy('id', 'desc')
            ->get();
    }
}
