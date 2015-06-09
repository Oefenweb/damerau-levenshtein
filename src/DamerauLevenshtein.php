<?php
namespace Oefenweb\DamerauLevenshtein;

/**
	* Compute Damerau-Levenshtein distance of two strings.
	*
	* For more information about algorithm
	* @see http://en.wikipedia.org/wiki/Damerau%E2%80%93Levenshtein_distance
	*/
class DamerauLevenshtein {

/**
 * First string.
 *
 * @var String
 */
	private $__compOne;

/**
 * Second string.
 *
 * @var String
 */
	private $__compTwo;

/**
 * Matrix for Damerau Levenshtein distance dynamic programming computation.
 *
 * @var int[][]
 */
	private $__matrix;

/**
 * Boolean flag determining whether is matrix computed for input strings.
 *
 * @var bool
 */
	private $__calculated = false;

/**
 * Cost of character insertion (to first string to match second string).
 *
 * @var int
 */
	private $__insCost = 1;

/**
 * Cost of character deletion (from first string to match second string).
 *
 * @var int
 */
	private $__delCost = 1;

/**
 * Substitution cost.
 *
 * @var int
 */
	private $__subCost = 1;

/**
 * Transposition cost.
 *
 * @var int
 */
	private $__transCost = 1;

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
	public function __construct($firstString, $secondString, $insCost = 1, $delCost = 1, $subCost = 1, $transCost = 1) {
		if (!empty($firstString) || !empty($secondString)) {
			$this->__compOne = $firstString;
			$this->__compTwo = $secondString;
		}

		$this->__insCost = $insCost;
		$this->__delCost = $delCost;
		$this->__subCost = $subCost;
		$this->__transCost = $transCost;
	}

/**
 * Returns computed matrix for given input strings.
 *
 * @return int[][] matrix
 */
	public function getMatrix() {
		$this->__setupMatrix();
		return $this->__matrix;
	}

/**
 * Returns similarity of strings, absolute number = Damerau Levenshtein distance.
 *
 * @return int
 */
	public function getSimilarity() {
		if (!$this->__calculated) {
			$this->__setupMatrix();
		}

		return $this->__matrix[mb_strlen($this->__compOne, 'UTF-8')][mb_strlen($this->__compTwo, 'UTF-8')];
	}

/**
 * Procedure to compute matrix for given input strings.
 *
 * @return void
 */
	private function __setupMatrix() {
		$cost = -1;
		$del = 0;
		$sub = 0;
		$ins = 0;
		$trans = 0;
		$this->__matrix = array(array());

		$oneSize = mb_strlen($this->__compOne, 'UTF-8');
		$twoSize = mb_strlen($this->__compTwo, 'UTF-8');
		for ($i = 0; $i <= $oneSize; $i += 1) {
			$this->__matrix[$i][0] = $i > 0 ? $this->__matrix[$i - 1][0] + $this->__delCost : 0;
		}

		for ($i = 0; $i <= $twoSize; $i += 1) {
			// Insertion actualy
			$this->__matrix[0][$i] = $i > 0 ? $this->__matrix[0][$i - 1] + $this->__insCost : 0;
		}

		for ($i = 1; $i <= $oneSize; $i += 1) {
			// Curchar for the first string
			$cOne = mb_substr($this->__compOne, $i - 1, 1, 'UTF-8');
			for ($j = 1; $j <= $twoSize; $j += 1) {
				// Curchar for the second string
				$cTwo = mb_substr($this->__compTwo, $j - 1, 1, 'UTF-8');

				// Compute substitution cost
				if ($this->_compare($cOne, $cTwo) == 0) {
					$cost = 0;
					$trans = 0;
				} else {
					$cost = $this->__subCost;
					$trans = $this->__transCost;
				}

				// Deletion cost
				$del = $this->__matrix[$i - 1][$j] + $this->__delCost;

				// Insertion cost
				$ins = $this->__matrix[$i][$j - 1] + $this->__insCost;

				// Substitution cost, 0 if same
				$sub = $this->__matrix[$i - 1][$j - 1] + $cost;

				// Compute optimal
				$this->__matrix[$i][$j] = min($del, $ins, $sub);

				// Transposition cost
				if (($i > 1) && ($j > 1)) {
					// Last two
					$ccOne = mb_substr($this->__compOne, $i - 2, 1, 'UTF-8');
					$ccTwo = mb_substr($this->__compTwo, $j - 2, 1, 'UTF-8');

					if ($this->_compare($cOne, $ccTwo) == 0 && $this->_compare($ccOne, $cTwo) == 0) {
						// Transposition cost is computed as minimal of two
						$this->__matrix[$i][$j] = min($this->__matrix[$i][$j], $this->__matrix[$i - 2][$j - 2] + $trans);
					}
				}
			}
		}

		$this->__calculated = true;
	}

/**
 * Returns maximal possible edit Damerau Levenshtein distance between texts.
 *
 * On common substring of same length perform substitution / insert + delete
 * (depends on what is cheaper), then on extra characters perform insertion / deletion
 *
 * @return int
 */
	public function getMaximalDistance() {
		$oneSize = mb_strlen($this->__compOne, 'UTF-8');
		$twoSize = mb_strlen($this->__compTwo, 'UTF-8');

		// Max cost, result value
		$maxCost = 0;

		// Is substitution cheaper that delete + insert?
		$subCost = min($this->__subCost, $this->__delCost + $this->__insCost);

		// Get common size
		$minSize = min($oneSize, $twoSize);
		$maxSize = max($oneSize, $twoSize);
		$extraSize = $maxSize - $minSize;

		// On common size perform substitution / delete + insert, what is cheaper
		$maxCost = $subCost * $minSize;

		// On resulting do insert/delete
		if ($oneSize > $twoSize) {
			// Delete extra characters
			$maxCost += $extraSize * $this->__delCost;
		} else {
			// Insert extra characters
			$maxCost += $extraSize * $this->__insCost;
		}

		return $maxCost;
	}

/**
 * Returns relative distance of input strings (computed with maximal possible distance).
 *
 * @return int
 */
	public function getRelativeDistance() {
		if (!$this->__calculated) {
			$this->__setupMatrix();
		}

		return 1 - (($this->getSimilarity()) / $this->getMaximalDistance());
	}

/**
 * Compares two characters from string (this method may be overriden in child class).
 *
 * @param string $firstCharacter First character
 * @param string $secondCharacter Second character
 * @return int
 */
	protected function _compare($firstCharacter, $secondCharacter) {
		return strcmp($firstCharacter, $secondCharacter);
	}

/**
 * Returns computed matrix for given input strings (For debugging purposes).
 *
 * @return string
 */
	public function displayMatrix() {
		$oneSize = mb_strlen($this->__compOne, 'UTF-8');
		$twoSize = mb_strlen($this->__compTwo, 'UTF-8');

		$out = "  " . $this->__compOne . "\n";
		for ($y = 0; $y <= $twoSize; $y += 1) {
			if ($y - 1 < 0) {
				$out .= " ";
			} else {
				$out .= (mb_substr($this->__compTwo, $y - 1, 1, 'UTF-8'));
			}

			for ($x = 0; $x <= $oneSize; $x += 1) {
				$out .= $this->__matrix[$x][$y];
			}

			$out .= "\n";
		}

		return $out;
	}

/**
 * Returns current cost of insertion operation.
 *
 * @return int
 */
	public function getInsCost() {
		return $this->__insCost;
	}

/**
 * Sets cost of insertion operation (insert characters to first string to match second string).
 *
 * @param int $insCost Cost of character insertion
 * @return void
 */
	public function setInsCost($insCost) {
		$this->__calculated = ($insCost == $this->__insCost) ? $this->__calculated : false;
		$this->__insCost = $insCost;
	}

/**
 * Returns current cost of deletion operation.
 *
 * @return int
 */
	public function getDelCost() {
		return $this->__delCost;
	}

/**
 * Sets cost of deletion operation (delete characters from first string to match second string).
 *
 * @param int $delCost Cost of character deletion
 * @return void
 */
	public function setDelCost($delCost) {
		$this->__calculated = ($delCost == $this->__delCost) ? $this->__calculated : false;
		$this->__delCost = $delCost;
	}

/**
 * Returns current cost of substitution operation.
 *
 * @return int
 */
	public function getSubCost() {
		return $this->__subCost;
	}

/**
 * Sets cost of substitution operation.
 *
 * @param int $subCost Cost of character substitution
 * @return void
 */
	public function setSubCost($subCost) {
		$this->__calculated = ($subCost == $this->__subCost) ? $this->__calculated : false;
		$this->__subCost = $subCost;
	}

/**
 * Returns current cost of transposition operation.
 *
 * @return int
 */
	public function getTransCost() {
		return $this->__transCost;
	}

/**
 * Sets cost of transposition operation.
 *
 * @param int $transCost Cost of character transposition
 * @return void
 */
	public function setTransCost($transCost) {
		$this->__calculated = ($transCost == $this->__transCost) ? $this->__calculated : false;
		$this->__transCost = $transCost;
	}

}
