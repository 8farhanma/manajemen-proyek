<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\ContentRankingService;

class Content extends Model
{
    protected $fillable = [
        'title',
        'description',
        'views',
        'likes',
        'comments',
    ];

    protected $attributes = [
        'views' => 0,
        'likes' => 0,
        'comments' => 0,
    ];

    public static function getPopularContent($limit = 10)
    {
        $contents = self::all();
        $rankingService = new ContentRankingService();
        $rankings = $rankingService->rankContents($contents);
        
        // Get top N contents
        $topContentIds = array_slice($rankings, 0, $limit);
        $ids = array_column($topContentIds, 'content_id');
        
        // Fetch content details with scores
        $popularContent = self::whereIn('id', $ids)
            ->get()
            ->map(function ($content) use ($rankings) {
                $ranking = collect($rankings)->first(function ($rank) use ($content) {
                    return $rank['content_id'] === $content->id;
                });
                $content->topsis_score = $ranking['score'];
                return $content;
            });

        return $popularContent;
    }
}
