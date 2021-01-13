<?php
namespace Oefenweb\DamerauLevenshtein;

/**
 * Compute Damerau-Levenshtein distance of two strings.
 *
 * For more information about algorithm
 * @see http://en.wikipedia.org/wiki/Damerau%E2%80%93Levenshtein_distance
 */
class DamerauLevenshtein
{

    /**
     * First string.
     *
     * @var String
     */
    private $compOne;

    /**
     * Second string.
     *
     * @var String
     */
    private $compTwo;

    /**
     * Length of first string.
     *
     * @var int
     */
    private $compOneLength = 0;

    /**
     * Length of second string.
     *
     * @var int
     */
    private $compTwoLength = 0;

    /**
     * Matrix for Damerau Levenshtein distance dynamic programming computation.
     *
     * @var int[][]
     */
    private $matrix;

    /**
     * Boolean flag determining whether is matrix computed for input strings.
     *
     * @var bool
     */
    private $calculated = false;

    /**
     * Cost of character insertion (to first string to match second string).
     *
     * @var int
     */
    private $insCost = 1;

    /**
     * Cost of character deletion (from first string to match second string).
     *
     * @var int
     */
    private $delCost = 1;

    /**
     * Substitution cost.
     *
     * @var int
     */
    private $subCost = 1;

    /**
     * Transposition cost.
     *
     * @var int
     */
    private $transCost = 1;

    /**
     * Constructor.
     *
     * @param string $firstString first string to compute distance
     * @param string $secondString second string to compute distance
     * @param int $insCost Cost of character insertion
     * @param int $delCost Cost of character deletion
     * @param int $subCost Substitution cost
     * @param int $transCost Transposition cost
     */
    public function __construct(
        string $firstString,
        string $secondString,
        int $insCost = 1,
        int $delCost = 1,
        int $subCost = 1,
        int $transCost = 1
    ) {
        if (!empty($firstString) || !empty($secondString)) {
            $this->compOne = $firstString;
            $this->compOneLength = (int)mb_strlen($this->compOne, 'UTF-8');
            $this->compTwo = $secondString;
            $this->compTwoLength = (int)mb_strlen($this->compTwo, 'UTF-8');
        }

        $this->insCost = $insCost;
        $this->delCost = $delCost;
        $this->subCost = $subCost;
        $this->transCost = $transCost;
    }

    /**
     * Returns computed matrix for given input strings.
     *
     * @return int[][] matrix
     */
    public function getMatrix(): array
    {
        if (!$this->calculated) {
            $this->setupMatrix();
        }

        return $this->matrix;
    }

    /**
     * Returns similarity of strings, absolute number = Damerau Levenshtein distance.
     *
     * @return int
     */
    public function getSimilarity(): int
    {
        if (!$this->calculated) {
            $this->setupMatrix();
        }

        return $this->matrix[$this->compOneLength][$this->compTwoLength];
    }

    /**
     * Procedure to compute matrix for given input strings.
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function setupMatrix(): void
    {
        $this->matrix = [[]];

        for ($i = 0; $i <= $this->compOneLength; $i += 1) {
            // @phan-suppress-next-line PhanTypeInvalidDimOffset
            $this->matrix[$i][0] = $i > 0 ? $this->matrix[$i - 1][0] + $this->delCost : 0;
        }

        for ($i = 0; $i <= $this->compTwoLength; $i += 1) {
            // Insertion actualy
            $this->matrix[0][$i] = $i > 0 ? $this->matrix[0][$i - 1] + $this->insCost : 0;
        }

        for ($i = 1; $i <= $this->compOneLength; $i += 1) {
            // Curchar for the first string
            $cOne = (string)mb_substr($this->compOne, $i - 1, 1, 'UTF-8');
            for ($j = 1; $j <= $this->compTwoLength; $j += 1) {
                // Curchar for the second string
                $cTwo = (string)mb_substr($this->compTwo, $j - 1, 1, 'UTF-8');

                // Compute substitution cost
                if ($this->compare($cOne, $cTwo) === 0) {
                    $cost = 0;
                    $trans = 0;
                } else {
                    $cost = $this->subCost;
                    $trans = $this->transCost;
                }

                // Deletion cost
                // @phan-suppress-next-line PhanTypeInvalidDimOffset, PhanTypeInvalidLeftOperandOfAdd
                $del = $this->matrix[$i - 1][$j] + $this->delCost;

                // Insertion cost

                // @codingStandardsIgnoreLine Generic.Files.LineLength
                // @phan-suppress-next-line PhanTypeArraySuspiciousNull, PhanTypeInvalidDimOffset, PhanTypeInvalidLeftOperandOfAdd
                $ins = $this->matrix[$i][$j - 1] + $this->insCost;

                // Substitution cost, 0 if same
                $sub = $this->matrix[$i - 1][$j - 1] + $cost;

                // Compute optimal
                $this->matrix[$i][$j] = min($del, $ins, $sub);

                // Transposition cost
                if ($i > 1 && $j > 1) {
                    // Last two
                    // @phan-suppress-next-line PhanPartialTypeMismatchArgumentInternal
                    $ccOne = (string)mb_substr($this->compOne, $i - 2, 1, 'UTF-8');
                    // @phan-suppress-next-line PhanPartialTypeMismatchArgumentInternal
                    $ccTwo = (string)mb_substr($this->compTwo, $j - 2, 1, 'UTF-8');

                    if ($this->compare($cOne, $ccTwo) === 0 && $this->compare($ccOne, $cTwo) === 0) {
                        // Transposition cost is computed as minimal of two
                        // @phan-suppress-next-line PhanPartialTypeMismatchArgumentInternal
                        $this->matrix[$i][$j] = min($this->matrix[$i][$j], $this->matrix[$i - 2][$j - 2] + $trans);
                    }
                }
            }
        }

        $this->calculated = true;
    }

    /**
     * Returns maximal possible edit Damerau Levenshtein distance between texts.
     *
     * On common substring of same length perform substitution / insert + delete
     * (depends on what is cheaper), then on extra characters perform insertion / deletion
     *
     * @return int
     */
    public function getMaximalDistance(): int
    {
        // Is substitution cheaper that delete + insert?
        $subCost = min($this->subCost, $this->delCost + $this->insCost);

        // Get common size
        $minSize = min($this->compOneLength, $this->compTwoLength);
        $maxSize = max($this->compOneLength, $this->compTwoLength);
        $extraSize = $maxSize - $minSize;

        // On common size perform substitution / delete + insert, what is cheaper
        $maxCost = $subCost * $minSize;

        // On resulting do insert/delete
        if ($this->compOneLength > $this->compTwoLength) {
            // Delete extra characters
            $maxCost += $extraSize * $this->delCost;
        } else {
            // Insert extra characters
            $maxCost += $extraSize * $this->insCost;
        }

        return (int)$maxCost;
    }

    /**
     * Returns relative distance of input strings (computed with maximal possible distance).
     *
     * @return float
     */
    public function getRelativeDistance(): float
    {
        if (!$this->calculated) {
            $this->setupMatrix();
        }

        return (float)(1 - ($this->getSimilarity() / $this->getMaximalDistance()));
    }

    /**
     * Compares two characters from string (this method may be overridden in child class).
     *
     * @param string $firstCharacter First character
     * @param string $secondCharacter Second character
     * @return int
     */
    protected function compare(string $firstCharacter, string $secondCharacter): int
    {
        return strcmp($firstCharacter, $secondCharacter);
    }

    /**
     * Returns computed matrix for given input strings (For debugging purposes).
     *
     * @return string
     */
    public function displayMatrix(): string
    {
        if (!$this->calculated) {
            $this->setupMatrix();
        }

        $out = '  ' . $this->compOne . PHP_EOL;
        for ($y = 0; $y <= $this->compTwoLength; $y += 1) {
            if ($y - 1 < 0) {
                $out .= ' ';
            } else {
                $out .= (string)mb_substr($this->compTwo, $y - 1, 1, 'UTF-8');
            }

            for ($x = 0; $x <= $this->compOneLength; $x += 1) {
                $out .= $this->matrix[$x][$y];
            }

            $out .= PHP_EOL;
        }

        return $out;
    }

    /**
     * Returns current cost of insertion operation.
     *
     * @return int
     */
    public function getInsCost(): int
    {
        return $this->insCost;
    }

    /**
     * Sets cost of insertion operation (insert characters to first string to match second string).
     *
     * @param int $insCost Cost of character insertion
     * @return void
     */
    public function setInsCost(int $insCost): void
    {
        $this->calculated = $insCost === $this->insCost ? $this->calculated : false;
        $this->insCost = $insCost;
    }

    /**
     * Returns current cost of deletion operation.
     *
     * @return int
     */
    public function getDelCost(): int
    {
        return $this->delCost;
    }

    /**
     * Sets cost of deletion operation (delete characters from first string to match second string).
     *
     * @param int $delCost Cost of character deletion
     * @return void
     */
    public function setDelCost(int $delCost): void
    {
        $this->calculated = $delCost === $this->delCost ? $this->calculated : false;
        $this->delCost = $delCost;
    }

    /**
     * Returns current cost of substitution operation.
     *
     * @return int
     */
    public function getSubCost(): int
    {
        return $this->subCost;
    }

    /**
     * Sets cost of substitution operation.
     *
     * @param int $subCost Cost of character substitution
     * @return void
     */
    public function setSubCost(int $subCost): void
    {
        $this->calculated = $subCost === $this->subCost ? $this->calculated : false;
        $this->subCost = $subCost;
    }

    /**
     * Returns current cost of transposition operation.
     *
     * @return int
     */
    public function getTransCost(): int
    {
        return $this->transCost;
    }

    /**
     * Sets cost of transposition operation.
     *
     * @param int $transCost Cost of character transposition
     * @return void
     */
    public function setTransCost(int $transCost): void
    {
        $this->calculated = $transCost === $this->transCost ? $this->calculated : false;
        $this->transCost = $transCost;
    }
}
