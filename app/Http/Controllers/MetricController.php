<?php

namespace App\Http\Controllers;

class MetricController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Metrics/Index');
    }
}
