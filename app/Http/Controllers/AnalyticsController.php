<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $contents = Content::getPopularContent();
        
        // Get TOPSIS scores and prepare data for bar chart
        $chartData = $contents->map(function ($content) {
            return [
                'name' => $content->title,
                'score' => $content->topsis_score,
            ];
        })->sortByDesc('score')->values();

        // Prepare data for pie chart
        $pieChartData = $this->preparePieChartData($contents);

        // Prepare data for line chart
        $lineChartData = $this->prepareLineChartData($contents);

        // Prepare data for radar chart
        $radarChartData = $this->prepareRadarChartData($contents);

        return view('analytics.dashboard', [
            'chartData' => $chartData,
            'pieChartData' => $pieChartData,
            'lineChartData' => $lineChartData,
            'radarChartData' => $radarChartData
        ]);
    }

    private function preparePieChartData($contents)
    {
        $categories = [
            'Excellent' => 0,
            'Good' => 0,
            'Average' => 0,
            'Poor' => 0
        ];

        foreach ($contents as $content) {
            $score = $content->topsis_score;
            if ($score >= 0.75) {
                $categories['Excellent']++;
            } elseif ($score >= 0.5) {
                $categories['Good']++;
            } elseif ($score >= 0.25) {
                $categories['Average']++;
            } else {
                $categories['Poor']++;
            }
        }

        return [
            'labels' => array_keys($categories),
            'data' => array_values($categories)
        ];
    }

    private function prepareLineChartData($contents)
    {
        $timeSeriesData = $contents->sortBy('created_at')
            ->map(function ($content) {
                return [
                    'date' => $content->created_at->format('Y-m-d'),
                    'title' => $content->title,
                    'score' => $content->topsis_score
                ];
            })
            ->groupBy('date')
            ->map(function ($group) {
                return [
                    'average_score' => $group->avg('score'),
                    'contents' => $group->values()
                ];
            });

        return [
            'labels' => $timeSeriesData->keys(),
            'datasets' => [
                [
                    'label' => 'Average TOPSIS Score',
                    'data' => $timeSeriesData->pluck('average_score'),
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'fill' => true
                ]
            ],
            'contentsByDate' => $timeSeriesData->map->contents
        ];
    }

    private function prepareRadarChartData($contents)
    {
        // Define criteria labels
        $criteria = [
            'views' => 'Views',
            'likes' => 'Likes',
            'comments' => 'Comments'
        ];

        // Get max values for normalization
        $maxValues = [
            'views' => $contents->max('views') ?: 1,
            'likes' => $contents->max('likes') ?: 1,
            'comments' => $contents->max('comments') ?: 1
        ];

        // Prepare datasets for top 5 contents
        $datasets = $contents->take(5)->map(function ($content) use ($criteria, $maxValues) {
            $color = $this->generateRandomColor();
            
            return [
                'label' => $content->title,
                'data' => collect($criteria)->map(function ($label, $key) use ($content, $maxValues) {
                    // Normalize values between 0 and 1
                    return $content->$key / $maxValues[$key];
                })->values()->all(),
                'backgroundColor' => str_replace('1)', '0.2)', $color),
                'borderColor' => $color,
                'pointBackgroundColor' => $color,
                'pointBorderColor' => '#fff',
                'pointHoverBackgroundColor' => '#fff',
                'pointHoverBorderColor' => $color
            ];
        })->values();

        return [
            'labels' => array_values($criteria),
            'datasets' => $datasets
        ];
    }

    private function generateRandomColor()
    {
        $colors = [
            'rgba(255, 99, 132, 1)',   // Red
            'rgba(54, 162, 235, 1)',   // Blue
            'rgba(255, 206, 86, 1)',   // Yellow
            'rgba(75, 192, 192, 1)',   // Teal
            'rgba(153, 102, 255, 1)',  // Purple
        ];
        
        static $index = 0;
        return $colors[$index++ % count($colors)];
    }
}
