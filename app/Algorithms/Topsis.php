<?php

namespace App\Algorithms;

class Topsis
{
    private array $matrix;        // Decision matrix (D)
    private array $weights;       // Criteria weights
    private array $isBenefit;     // Criteria type (benefit/cost)
    private array $alternatives;  // List of alternatives (Ai)
    private array $criteria;      // List of criteria (Fj)
    private array $columnDivisors;

    /**
     * Initialize TOPSIS with decision matrix and criteria information
     *
     * @param array $alternatives Array of alternatives (Ai)
     * @param array $criteria Array of criteria (Fj)
     * @param array $matrix Decision matrix where matrix[i][j] represents the rating of alternative i for criteria j
     * @param array $weights Weights for each criterion
     * @param array $isBenefit Array indicating if each criterion is benefit (true) or cost (false)
     */
    public function __construct(array $alternatives, array $criteria, array $matrix, array $weights, array $isBenefit)
    {
        // Validate input dimensions
        if (empty($matrix) || empty($weights) || empty($isBenefit)) {
            throw new \InvalidArgumentException("Matrix, weights, and isBenefit arrays cannot be empty");
        }

        if (count($weights) !== count($criteria) || count($isBenefit) !== count($criteria)) {
            throw new \InvalidArgumentException("Number of weights and benefit indicators must match number of criteria");
        }

        if (count($matrix) !== count($alternatives)) {
            throw new \InvalidArgumentException("Number of matrix rows must match number of alternatives");
        }

        foreach ($matrix as $row) {
            if (count($row) !== count($criteria)) {
                throw new \InvalidArgumentException("Each matrix row must have ratings for all criteria");
            }
        }

        $this->matrix = $matrix;
        $this->weights = $weights;
        $this->isBenefit = $isBenefit;
        $this->alternatives = $alternatives;
        $this->criteria = $criteria;
        $this->columnDivisors = [];
    }

    /**
     * Create decision matrix from raw data
     *
     * @param array $data Array of objects/arrays containing criteria values
     * @param array $criteriaMap Map of criteria keys to use from data
     * @return array Decision matrix
     */
    public static function createDecisionMatrix(array $data, array $criteriaMap): array
    {
        $matrix = [];
        foreach ($data as $i => $item) {
            $row = [];
            foreach ($criteriaMap as $criterion) {
                $row[] = is_object($item) ? $item->$criterion : $item[$criterion];
            }
            $matrix[$i] = $row;
        }
        return $matrix;
    }

    public function calculate(): array
    {
        if (empty($this->matrix)) {
            return [];
        }

        // Step 1: Normalize the decision matrix
        $normalized = $this->calculateNormalizedMatrix();

        // Step 2: Calculate weighted normalized matrix
        $weighted = $this->calculateWeightedNormalizedMatrix();

        // Step 3: Determine ideal solutions
        [$idealPositive, $idealNegative] = $this->findIdealSolutions($weighted);

        // Step 4: Calculate distances
        [$distPositive, $distNegative] = $this->calculateDistances($weighted, $idealPositive, $idealNegative);

        // Step 5: Calculate relative closeness
        $preferences = $this->calculatePreferences($distPositive, $distNegative);

        // Create results array with alternative information
        $results = [];
        foreach ($preferences as $i => $score) {
            $results[] = [
                'alternative' => $this->alternatives[$i],
                'score' => $score,
                'criteria_values' => $this->matrix[$i]
            ];
        }

        // Sort results by score in descending order
        usort($results, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Add rank to each result
        foreach ($results as $index => $result) {
            $results[$index]['rank'] = $index + 1;
        }

        return $results;
    }

    private function calculateNormalizedMatrix(): array
    {
        $normalizedMatrix = [];
        $columnSums = array_fill(0, count($this->criteria), 0);

        // Calculate sum of squares for each column (criterion)
        foreach ($this->matrix as $i => $row) {
            foreach ($row as $j => $value) {
                $columnSums[$j] += pow($value, 2);
            }
        }

        // Store the divisors, ensuring no division by zero
        foreach ($columnSums as $j => $sum) {
            // If sum is 0, all values in column are 0, so divisor will be 1 to maintain 0 values
            $this->columnDivisors[$this->criteria[$j]] = $sum > 0 ? sqrt($sum) : 1;
        }

        // Calculate normalized values
        foreach ($this->matrix as $i => $row) {
            $normalizedMatrix[$i] = [];
            foreach ($row as $j => $value) {
                $normalizedMatrix[$i][$j] = $value / $this->columnDivisors[$this->criteria[$j]];
            }
        }

        return $normalizedMatrix;
    }

    /**
     * Calculate the weighted normalized decision matrix (V)
     * Using the formula: vij = wj * rij
     * where:
     * vij = weighted normalized value for alternative i and criterion j
     * wj = weight of criterion j
     * rij = normalized value for alternative i and criterion j
     *
     * @return array Weighted normalized decision matrix V
     */
    private function calculateWeightedNormalizedMatrix(): array
    {
        // Get the normalized matrix (R)
        $normalizedMatrix = $this->calculateNormalizedMatrix();
        $weightedMatrix = [];

        // Calculate vij = wj * rij for each element
        foreach ($this->alternatives as $i => $alternative) {
            $weightedMatrix[$i] = [];
            foreach ($this->criteria as $j => $criterion) {
                // Apply the formula vij = wj * rij
                $weightedMatrix[$i][$j] = $this->weights[$j] * $normalizedMatrix[$i][$j];
            }
        }

        return $weightedMatrix;
    }

    /**
     * Determine the positive ideal solution (V+) and negative ideal solution (V-) for each criterion
     *
     * For benefit criteria:
     * - V+ is the maximum value
     * - V- is the minimum value
     *
     * For cost criteria:
     * - V+ is the minimum value
     * - V- is the maximum value
     *
     * @param array $weighted The weighted normalized decision matrix
     * @return array Array containing positive and negative ideal solutions
     */
    private function findIdealSolutions(array $weighted): array
    {
        $criteriaCount = count($this->criteria);
        $idealPositive = array_fill(0, $criteriaCount, 0);
        $idealNegative = array_fill(0, $criteriaCount, 0);

        if (!empty($weighted)) {
            for ($j = 0; $j < $criteriaCount; $j++) {
                // Get all values for current criterion
                $column = array_column($weighted, $j);

                if ($this->isBenefit[$j]) {
                    // For benefit criteria, V+ = max value, V- = min value
                    $idealPositive[$j] = max($column);
                    $idealNegative[$j] = min($column);
                } else {
                    // For cost criteria, V+ = min value, V- = max value
                    $idealPositive[$j] = min($column);
                    $idealNegative[$j] = max($column);
                }
            }
        }

        return [$idealPositive, $idealNegative];
    }

    /**
     * Calculate distances from positive and negative ideal solutions
     *
     * @param array $weighted Weighted normalized decision matrix
     * @param array $idealPositive Positive ideal solution
     * @param array $idealNegative Negative ideal solution
     * @return array Array containing distances [distPositive, distNegative]
     */
    public function calculateDistances(array $weighted, array $idealPositive, array $idealNegative): array
    {
        $distPositive = [];
        $distNegative = [];

        // Calculate distances for each alternative
        foreach ($this->alternatives as $i => $alternative) {
            $sumPositive = 0;
            $sumNegative = 0;

            // Calculate sum of squares of differences for each criterion
            foreach ($this->criteria as $j => $criterion) {
                // Distance from positive ideal (D+)
                $sumPositive += pow($weighted[$i][$j] - $idealPositive[$j], 2);
                
                // Distance from negative ideal (D-)
                $sumNegative += pow($weighted[$i][$j] - $idealNegative[$j], 2);
            }

            // Calculate square root for final distances
            $distPositive[$i] = sqrt($sumPositive);
            $distNegative[$i] = sqrt($sumNegative);
        }

        return [$distPositive, $distNegative];
    }

    /**
     * Calculate relative closeness to the ideal solution
     * Using the formula: Ci = Di- / (Di+ + Di-)
     * where:
     * Ci = relative closeness coefficient
     * Di- = distance from negative ideal solution
     * Di+ = distance from positive ideal solution
     *
     * @param array $distPositive Array of distances from positive ideal solution (Di+)
     * @param array $distNegative Array of distances from negative ideal solution (Di-)
     * @return array Array of relative closeness coefficients
     */
    private function calculatePreferences(array $distPositive, array $distNegative): array
    {
        $preferences = [];
        foreach ($distPositive as $i => $dPos) {
            $denominator = $dPos + $distNegative[$i];
            // Calculate relative closeness coefficient (Ci)
            // If denominator is 0, set Ci to 0 to avoid division by zero
            $preferences[$i] = $denominator != 0 ? $distNegative[$i] / $denominator : 0;
        }
        return $preferences;
    }

    /**
     * Get the normalized decision matrix
     *
     * @return array The normalized decision matrix
     */
    public function getNormalizedMatrix(): array
    {
        $normalizedMatrix = [];
        $columnSums = array_fill(0, count($this->criteria), 0);

        // Calculate sum of squares for each column (criterion)
        foreach ($this->matrix as $i => $row) {
            foreach ($row as $j => $value) {
                $columnSums[$j] += pow($value, 2);
            }
        }

        // Calculate normalized values using the formula rij = fij / sqrt(sum(fij^2))
        foreach ($this->matrix as $i => $row) {
            $normalizedMatrix[$i] = [];
            foreach ($row as $j => $value) {
                $divisor = sqrt($columnSums[$j]);
                $normalizedMatrix[$i][$j] = $divisor != 0 ? $value / $divisor : 0;
            }
        }

        return $normalizedMatrix;
    }

    /**
     * Get the original matrix with alternatives as keys
     *
     * @return array The original decision matrix with alternative names
     */
    public function getOriginalMatrix(): array
    {
        $matrix = [];
        foreach ($this->alternatives as $i => $alternative) {
            $matrix[$alternative] = $this->matrix[$i];
        }
        return $matrix;
    }

    /**
     * Get the criteria
     * @return array
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }

    /**
     * Get the alternatives
     * @return array
     */
    public function getAlternatives(): array
    {
        return $this->alternatives;
    }

    /**
     * Get the normalization divisors for each criterion
     *
     * @return array Array of divisors indexed by criterion
     */
    public function getNormalizationDivisors(): array
    {
        return $this->columnDivisors;
    }

    /**
     * Set weights for criteria
     *
     * @param array $weights Array of weights for each criterion
     * @throws \InvalidArgumentException if weights array is invalid
     */
    public function setWeights(array $weights): void
    {
        if (count($weights) !== count($this->criteria)) {
            throw new \InvalidArgumentException("Number of weights must match number of criteria");
        }

        // Validate weights are positive numbers
        foreach ($weights as $weight) {
            if (!is_numeric($weight) || $weight < 0) {
                throw new \InvalidArgumentException("Weights must be positive numbers");
            }
        }

        $this->weights = $weights;
    }

    /**
     * Set benefit/cost attributes for criteria
     *
     * @param array $isBenefit Array indicating if each criterion is benefit (true) or cost (false)
     * @throws \InvalidArgumentException if isBenefit array is invalid
     */
    public function setCriteriaAttributes(array $isBenefit): void
    {
        if (count($isBenefit) !== count($this->criteria)) {
            throw new \InvalidArgumentException("Number of attribute indicators must match number of criteria");
        }

        // Validate each value is boolean
        foreach ($isBenefit as $value) {
            if (!is_bool($value)) {
                throw new \InvalidArgumentException("Criteria attributes must be boolean values");
            }
        }

        $this->isBenefit = $isBenefit;
    }

    /**
     * Get the weighted normalized decision matrix
     *
     * @return array The weighted normalized decision matrix
     */
    public function getWeightedNormalizedMatrix(): array
    {
        return $this->calculateWeightedNormalizedMatrix();
    }

    /**
     * Get ideal solutions for the weighted normalized matrix
     *
     * @param array $weighted Weighted normalized decision matrix
     * @return array Array containing positive and negative ideal solutions
     */
    public function getIdealSolutions(array $weighted): array
    {
        return $this->findIdealSolutions($weighted);
    }

    /**
     * Get the distances from ideal solutions
     * 
     * @return array Array containing [distancePositive, distanceNegative]
     */
    public function getDistances(): array
    {
        $weighted = $this->getWeightedNormalizedMatrix();
        [$idealPositive, $idealNegative] = $this->getIdealSolutions($weighted);
        return $this->calculateDistances($weighted, $idealPositive, $idealNegative);
    }
}
