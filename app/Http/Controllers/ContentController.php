<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use App\Algorithms\Topsis;

class ContentController extends Controller
{
    public function index()
    {
        $popularContent = Content::getPopularContent(5);
        $allContent = Content::orderBy('created_at', 'desc')->paginate(15);

        return view('content.index', compact('popularContent', 'allContent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'views' => 'nullable|integer|min:0',
            'likes' => 'nullable|integer|min:0',
            'comments' => 'nullable|integer|min:0',
        ]);

        // Set default values for metrics if not provided
        $validated['views'] = $validated['views'] ?? 0;
        $validated['likes'] = $validated['likes'] ?? 0;
        $validated['comments'] = $validated['comments'] ?? 0;

        Content::create($validated);

        return redirect()->route('content.index')->with('success', 'Content created successfully');
    }

    public function like($id)
    {
        $content = Content::findOrFail($id);
        $content->increment('likes');
        return response()->json(['likes' => $content->likes]);
    }

    public function comment($id)
    {
        $content = Content::findOrFail($id);
        $content->increment('comments');
        return response()->json(['comments' => $content->comments]);
    }

    public function view($id)
    {
        $content = Content::findOrFail($id);
        $content->increment('views');
        return response()->json(['views' => $content->views]);
    }

    /**
     * Display the normalization divisors.
     *
     * @return \Illuminate\View\View
     */
    public function showNormalizationDivisors()
    {
        $topsis = $this->initializeTopsis();
        $topsis->calculate(); // This will trigger the calculation of divisors
        
        return view('content.normalization-divisors', [
            'divisors' => $topsis->getNormalizationDivisors()
        ]);
    }

    /**
     * Display the normalized decision matrix.
     *
     * @return \Illuminate\View\View
     */
    public function showNormalizedMatrix()
    {
        $topsis = $this->initializeTopsis();
        
        $normalizedMatrix = [];
        foreach ($topsis->getNormalizedMatrix() as $i => $row) {
            $normalizedMatrix[$topsis->getAlternatives()[$i]] = $row;
        }
        
        return view('content.normalized-matrix', [
            'normalizedMatrix' => $normalizedMatrix
        ]);
    }

    /**
     * Display the weighted normalized decision matrix.
     */
    public function weightedNormalizedMatrix()
    {
        $topsis = $this->initializeTopsis();
        $topsis->calculate(); // This will calculate everything including the weighted matrix
        
        return view('content.weighted-normalized-matrix', [
            'weightedMatrix' => $topsis->getWeightedNormalizedMatrix(),
            'alternatives' => $topsis->getAlternatives()
        ]);
    }

    /**
     * Initialize TOPSIS with content data
     * 
     * @return \App\Algorithms\Topsis
     */
    private function initializeTopsis()
    {
        $contents = Content::all();
        
        return new Topsis(
            $contents->pluck('title')->toArray(),
            ['likes', 'comments', 'views'],
            $contents->map(function ($content) {
                return [$content->likes, $content->comments, $content->views];
            })->toArray(),
            [0.4, 0.3, 0.3], // weights for likes, comments, views
            [true, true, true] // all are benefit criteria
        );
    }

    public function showIdealSolutions()
    {
        $topsis = $this->initializeTopsis();
        $weighted = $topsis->getWeightedNormalizedMatrix();
        [$idealPositive, $idealNegative] = $topsis->getIdealSolutions($weighted);
        
        return view('content.ideal-solutions', [
            'criteria' => $topsis->getCriteria(),
            'idealPositive' => $idealPositive,
            'idealNegative' => $idealNegative
        ]);
    }

    public function showSeparationMeasures()
    {
        $topsis = $this->initializeTopsis();
        [$distancePositive, $distanceNegative] = $topsis->getDistances();
        
        return view('content.separation-measures', [
            'alternatives' => array_map(function($alt) {
                return ['name' => $alt];
            }, $topsis->getAlternatives()),
            'distancePositive' => $distancePositive,
            'distanceNegative' => $distanceNegative
        ]);
    }

    /**
     * Display the relative closeness matrix and rankings.
     *
     * @return \Illuminate\View\View
     */
    public function showRelativeCloseness()
    {
        $topsis = $this->initializeTopsis();
        $results = $topsis->calculate();
        
        return view('content.relative-closeness', [
            'results' => $results
        ]);
    }

    public function topsis()
    {
        $topsis = $this->initializeTopsis();
        $topsis->calculate();
        
        // Get normalized matrix
        $normalizedMatrix = $topsis->getNormalizedMatrix();
        
        // Get weighted normalized matrix
        $weightedMatrix = $topsis->getWeightedNormalizedMatrix();
        
        // Get ideal solutions
        [$idealPositive, $idealNegative] = $topsis->getIdealSolutions($weightedMatrix);
        
        // Get separation measures
        [$distancePositive, $distanceNegative] = $topsis->getDistances();
        
        // Get relative closeness and results
        $results = $topsis->calculate();
        
        // Get alternatives (content titles)
        $alternatives = Content::all()->pluck('title')->toArray();
        
        return view('content.topsis', [
            'divisors' => $topsis->getNormalizationDivisors(),
            'normalizedMatrix' => array_combine($alternatives, $normalizedMatrix),
            'weightedMatrix' => $weightedMatrix,
            'alternatives' => $alternatives,
            'criteria' => ['Likes', 'Comments', 'Views'],
            'idealPositive' => $idealPositive,
            'idealNegative' => $idealNegative,
            'distancePositive' => $distancePositive,
            'distanceNegative' => $distanceNegative,
            'results' => array_map(function($alt, $score) {
                return ['alternative' => $alt, 'score' => $score];
            }, $alternatives, array_column($results, 'score'))
        ]);
    }

    /**
     * Update the specified content.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $content = Content::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'views' => 'nullable|integer|min:0',
            'likes' => 'nullable|integer|min:0',
            'comments' => 'nullable|integer|min:0',
        ]);

        $content->update($validated);

        return redirect()->route('content.index')
            ->with('success', 'Content updated successfully');
    }

    /**
     * Remove the specified content.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $content = Content::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content deleted successfully'
        ]);
    }
}
