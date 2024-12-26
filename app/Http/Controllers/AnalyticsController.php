<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $contents = Content::getPopularContent();
        
        // Get TOPSIS scores and prepare data for charts
        $chartData = $contents->map(function ($content) {
            return [
                'name' => $content->title,
                'score' => $content->topsis_score,
            ];
        })->sortByDesc('score')->values();

        return view('analytics.dashboard', [
            'chartData' => $chartData
        ]);
    }
}
