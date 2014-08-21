<?php
require sprintf('%s/../src/DamerauLevenshtein.php', dirname(__FILE__));

class DamerauLevenshteinTest extends PHPUnit_Framework_TestCase {

/**
 * Test for getSimilarity.
 *
 * @return void
 */
	public function testGetSimilarity() {
		$inputs = array(
			array('foo', 'foo'),
			array('foo', 'fooo'),
			array('foo', 'bar'),

			array('123', '12'),
			array('qwe', 'qwa'),
			array('awe', 'qwe'),
			array('фыв', 'фыа'),
			array('vvvqw', 'vvvwq'),
			array('qw', 'wq'),
			array('qq', 'ww'),
			array('qw', 'qw'),
			array('пионер', 'плеер'),
			array('пионер', 'пионеер'),
			array('пионер', 'поинер'),
			array('pioner', 'poner'),
			array('пионер', 'понер'),
		);
		$outputs = array(
			0,
			1,
			3,

			1,
			1,
			1,
			1,
			1,
			1,
			2,
			0,
			3,
			1,
			1,
			1,
			1,
		);

		foreach ($inputs as $i => $input) {
			$DamerauLevenshtein = new DamerauLevenshtein($input[0], $input[1]);
			$result = $DamerauLevenshtein->getSimilarity();
			$expected = $outputs[$i];

			$this->assertEquals($expected, $result);
		}
	}
}