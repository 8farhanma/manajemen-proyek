<?php

namespace App\Services;

use App\Algorithms\Topsis;
use Illuminate\Support\Collection;

class ContentRankingService
{
    private array $criteria = ['views', 'likes', 'comments'];
    private array $weights = [0.4, 0.35, 0.25]; // Weights for views, likes, comments
    private array $isBenefit = [true, true, true]; // All are benefit criteria

    public function rankContents(Collection $contents): array
    {
        if ($contents->isEmpty()) {
            return [];
        }

        // Extract alternatives (content items)
        $alternatives = $contents->map(function($content) {
            return [
                'content_id' => $content->id,
                'title' => $content->title
            ];
        })->toArray();

        // Create decision matrix using the criteria map
        $matrix = Topsis::createDecisionMatrix($contents->toArray(), $this->criteria);

        try {
            // Initialize TOPSIS with our data
            $topsis = new Topsis(
                alternatives: $alternatives,
                criteria: $this->criteria,
                matrix: $matrix,
                weights: $this->weights,
                isBenefit: $this->isBenefit
            );

            // Calculate rankings
            $results = $topsis->calculate();

            // Transform results to include content ID and score
            return array_map(function($result) {
                return [
                    'content_id' => $result['alternative']['content_id'],
                    'score' => $result['score']
                ];
            }, $results);

        } catch (\Exception $e) {
            \Log::error('Error in TOPSIS calculation: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get the decision matrix for the given contents
     */
    public function getDecisionMatrix(Collection $contents): array
    {
        return Topsis::createDecisionMatrix($contents->toArray(), $this->criteria);
    }

    /**
     * Get the criteria used for ranking
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }

    /**
     * Get the weights used for ranking
     */
    public function getWeights(): array
    {
        return $this->weights;
    }
}
