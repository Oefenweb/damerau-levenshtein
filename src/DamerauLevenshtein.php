<?php
/**
	* Compute Damerau-Levenshtein distance of two strings.
	*
	* For more information about algorithm
	* @see http://en.wikipedia.org/wiki/Damerau%E2%80%93Levenshtein_distance
	*/
class DamerauLevenshtein {

/**
 * First string
 *
 * @var String
 */
	private $compOne;

/**
 * Second string
 *
 * @var String
 */
	private $compTwo;

/**
 * Matrix for Damerau Levenshtein distance dynamic programming computation
 * @var int[][]
 */
	private $matrix;

/**
 * Boolean flag determining whether is matrix computed for input strings
 *
 * @var boolean
 */
	private $calculated = false;

/**
 * Cost of character insertion
 * (to first string to match second string)
 *
 * @var integer
 */
	private $insCost = 1;

/**
 * Cost of character deletion
 * (from first string to match second string)
 *
 * @var integer
 */
	private $delCost = 1;

/**
 * Substitution cost
 *
 * @var integer
 */
	private $subCost = 1;

/**
 * Transposition cost
 *
 * @var integer
 */
	private $transCost = 1;

/**
 * Constructor
 *
 * @param string $a first string to compute distance
 * @param string $b second string to compute distance
 * @param integer $insCost
 * @param integer $delCost
 * @param integer $subCost
 * @param integer $transCost
 */
	public function __construct($a, $b, $insCost = 1, $delCost = 1, $subCost = 1, $transCost = 1) {
		if (!empty($a) || !empty($b)) {
			$this->compOne = $a;
			$this->compTwo = $b;
		}

		$this->insCost = $insCost;
		$this->delCost = $delCost;
		$this->subCost = $subCost;
		$this->transCost = $transCost;
	}

/**
 * Returns computed matrix for given input strings
 *
 * @return integer[][] matrix
 */
	public function getMatrix() {
			$this->setupMatrix();
			return $this->matrix;
	}

/**
 * Returns similarity of strings, absolute number = Damerau Levenshtein distance
 *
 * @return integer
 */
	public function getSimilarity() {
		if (!$this->calculated) {
			$this->setupMatrix();
		}

		return $this->matrix[mb_strlen($this->compOne, 'UTF-8')][mb_strlen($this->compTwo, 'UTF-8')];
	}

/**
 * Procedure to compute matrix for given input strings
 *
 * @return void
 */
	private function setupMatrix() {
		$cost = -1;
		$del = 0;
		$sub = 0;
		$ins = 0;
		$trans = 0;
		$this->matrix = array(array());

		$oneSize = mb_strlen($this->compOne, 'UTF-8');
		$twoSize = mb_strlen($this->compTwo, 'UTF-8');
		for ($i = 0; $i <= $oneSize; $i++) {
			$this->matrix[$i][0] = $i > 0 ? $this->matrix[$i - 1][0] + $this->delCost : 0;
		}

		for ($i = 0; $i <= $twoSize; $i++) {
			// insertion actualy
			$this->matrix[0][$i] = $i > 0 ? $this->matrix[0][$i - 1] + $this->insCost : 0;
		}

		for ($i = 1; $i <= $oneSize; $i++) {
			// curchar for the first string
			$cOne = mb_substr($this->compOne, $i - 1, 1, 'UTF-8');
			for ($j = 1; $j <= $twoSize; $j++) {
				// curchar for the second string
				$cTwo = mb_substr($this->compTwo, $j - 1, 1, 'UTF-8');

				// compute substitution cost
				if ($this->compare($cOne, $cTwo) == 0) {
					$cost = 0;
					$trans = 0;
				} else {
					$cost = $this->subCost;
					$trans = $this->transCost;
				}

				// deletion cost
				$del = $this->matrix[$i - 1][$j] + $this->delCost;

				// insertion cost
				$ins = $this->matrix[$i][$j - 1] + $this->insCost;

				// substitution cost
				// 0 if same
				$sub = $this->matrix[$i - 1][$j - 1] + $cost;

				// compute optimal
				$this->matrix[$i][$j] = min($del, $ins, $sub);

				// transposition cost
				if (($i > 1) && ($j > 1)) {
					// last two
					$ccOne = mb_substr($this->compOne, $i - 2, 1, 'UTF-8');
					$ccTwo = mb_substr($this->compTwo, $j - 2, 1, 'UTF-8');

					if ($this->compare($cOne, $ccTwo) == 0 && $this->compare($ccOne, $cTwo) == 0) {
						// transposition cost is computed as minimal of two
						$this->matrix[$i][$j] = min($this->matrix[$i][$j], $this->matrix[$i - 2][$j - 2] + $trans);
					}
				}
			}
		}

		$this->calculated = true;
		//displayMatrix();
	}

/**
 * Returns maximal possible edit Damerau Levenshtein distance between texts.
 *
 * On common substring of same length perform substitution / insert+delete
 * (depends on what is cheaper), then on extra characters perform insertion/deletion
 *
 * @return
 */
	public function getMaximalDistance() {
		$oneSize = mb_strlen($this->compOne, 'UTF-8');
		$twoSize = mb_strlen($this->compTwo, 'UTF-8');

		// amx cost, result value
		$maxCost = 0;

		// is substitution cheaper that delete+insert?
		$subCost = min($this->subCost, $this->delCost + $this->insCost);

		// get common size
		$minSize = min($oneSize, $twoSize);
		$maxSize = max($oneSize, $twoSize);
		$extraSize = $maxSize - $minSize;

		// on common size perform substitution / delete+insert, what is cheaper
		$maxCost = $subCost * $minSize;

		// on resulting do insert/delete
		if ($oneSize > $twoSize) {
			// delete extra characters
			$maxCost += $extraSize * $this->delCost;
		} else {
			// insert extra characters
			$maxCost += $extraSize * $this->insCost;
		}

		return $maxCost;
	}

/**
 * Returns relative distance of input strings
 *
 * (Computed with maximal possible distance)
 *
 * @return
 */
	public function getRelativeDistance() {
		if (!$this->calculated) {
			$this->setupMatrix();
		}

		return 1 - (($this->getSimilarity()) / $this->getMaximalDistance());
	}

/**
 * Compares two characters from string
 * (this method may be overriden in child class)
 *
 * @param string $a
 * @param string $b
 * @return
 */
	protected function compare($a, $b) {
		return strcmp($a, $b);
	}

/**
 * Returns computed matrix for given input strings
 * (For debugging purposes)
 *
 * @return string
 */
	public function displayMatrix() {
		$oneSize = mb_strlen($this->compOne, 'UTF-8');
		$twoSize = mb_strlen($this->compTwo, 'UTF-8');

		$out = "  " . $this->compOne . "\n";
		for ($y = 0; $y <= $twoSize; $y++) {
			if ($y - 1 < 0) {
				$out .= " ";
			} else {
				$out .= (mb_substr($this->compTwo, $y - 1, 1, 'UTF-8'));
			}

			for ($x = 0; $x <= $oneSize; $x++) {
				$out .= $this->matrix[$x][$y];
			}

			$out .= "\n";
		}

		return $out;
	}

/**
 * Returns current cost of insertion operation
 *
 * @return integer
 */
	public function getInsCost() {
		return $this->insCost;
	}

/**
 * Sets cost of insertion operation.
 * (Insert characters to first string to match second string)
 *
 * @param integer $insCost
 * @return void
 */
	public function setInsCost($insCost) {
		$this->calculated = ($insCost == $this->insCost) ? $this->calculated : false;
		$this->insCost = $insCost;
	}

/**
 * Returns current cost of deletion operation
 *
 * @return integer
 */
	public function getDelCost() {
		return $this->delCost;
	}

/**
 * Sets cost of deletion operation.
 * (Delete characters from first string to match second string)
 *
 * @param integer $delCost
 * @return void
 */
	public function setDelCost($delCost) {
		$this->calculated = ($delCost == $this->delCost) ? $this->calculated : false;
		$this->delCost = $delCost;
	}

/**
 * Returns current cost of substitution operation
 *
 * @return integer
 */
	public function getSubCost() {
			return $this->subCost;
	}

/**
 * Sets cost of substitution operation.
 *
 * @param integer $subCost
 * @return void
 */
	public function setSubCost($subCost) {
		$this->calculated = ($subCost == $this->subCost) ? $this->calculated : false;
		$this->subCost = $subCost;
	}

/**
 * Returns current cost of transposition operation
 *
 * @return integer
 */
	public function getTransCost() {
		return $this->transCost;
	}

/**
 * Sets cost of transposition operation.
 *
 * @param integer $transCost
 * @return void
 */
	public function setTransCost($transCost) {
			$this->calculated = ($transCost == $this->transCost) ? $this->calculated : false;
			$this->transCost = $transCost;
	}

}
