<?php
namespace Oefenweb\DamerauLevenshtein\Test;

use Oefenweb\DamerauLevenshtein\DamerauLevenshtein;
use PHPUnit_Framework_TestCase;

class DamerauLevenshteinTest extends PHPUnit_Framework_TestCase {

/**
 * Test for `getSimilarity`.
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

			$this->assertSame($expected, $result);
		}
	}

/**
 * Test for `getInsCost`.
 *
 * @return void
 */
	public function testGetInsCost() {
		$firstString = 'foo';
		$secondString = 'bar';
		$insCost = 1;
		$delCost = 1;
		$subCost = 1;
		$transCost = 1;

		// Default insert cost

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
		$result = $DamerauLevenshtein->getInsCost();
		$expected = $insCost;

		$this->assertSame($expected, $result);

		// Non-default insert cost

		$insCost = 2;

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString, $insCost, $delCost, $subCost, $transCost);
		$result = $DamerauLevenshtein->getInsCost();
		$expected = $insCost;

		$this->assertSame($expected, $result);
	}

/**
 * Test for `getDelCost`.
 *
 * @return void
 */
	public function testGetDelCost() {
		$firstString = 'foo';
		$secondString = 'bar';
		$insCost = 1;
		$delCost = 1;
		$subCost = 1;
		$transCost = 1;

		// Default delete cost

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
		$result = $DamerauLevenshtein->getDelCost();
		$expected = $delCost;

		$this->assertSame($expected, $result);

		// Non-default delete cost

		$delCost = 2;

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString, $insCost, $delCost, $subCost, $transCost);
		$result = $DamerauLevenshtein->getDelCost();
		$expected = $delCost;

		$this->assertSame($expected, $result);
	}

/**
 * Test for `getSubCost`.
 *
 * @return void
 */
	public function testGetSubCost() {
		$firstString = 'foo';
		$secondString = 'bar';
		$insCost = 1;
		$delCost = 1;
		$subCost = 1;
		$transCost = 1;

		// Default substitution cost

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
		$result = $DamerauLevenshtein->getSubCost();
		$expected = $subCost;

		$this->assertSame($expected, $result);

		// Non-default substitution cost

		$subCost = 2;

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString, $insCost, $delCost, $subCost, $transCost);
		$result = $DamerauLevenshtein->getSubCost();
		$expected = $subCost;

		$this->assertSame($expected, $result);
	}

/**
 * Test for `getTransCost`.
 *
 * @return void
 */
	public function testGetTransCost() {
		$firstString = 'foo';
		$secondString = 'bar';
		$insCost = 1;
		$delCost = 1;
		$subCost = 1;
		$transCost = 1;

		// Default transposition cost

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
		$result = $DamerauLevenshtein->getTransCost();
		$expected = $transCost;

		$this->assertSame($expected, $result);

		// Non-default transposition cost

		$transCost = 2;

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString, $insCost, $delCost, $subCost, $transCost);
		$result = $DamerauLevenshtein->getTransCost();
		$expected = $transCost;

		$this->assertSame($expected, $result);
	}

/**
 * Test for `getRelativeDistance`.
 *
 * @return void
 */
	public function testGetRelativeDistance() {
		$delta = pow(10, -4);

		$firstString = 'O\'Callaghan';
		$secondString = 'OCallaghan';

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
		$result = $DamerauLevenshtein->getRelativeDistance();
		$expected = 0.90909090909091;
		$this->assertEquals($expected, $result, '', $delta);

		$firstString = 'Thom';
		$secondString = 'Mira';

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
		$result = $DamerauLevenshtein->getRelativeDistance();
		$expected = 0.0;
		$this->assertEquals($expected, $result, '', $delta);

		$firstString = 'Oldeboom';
		$secondString = 'Ven';

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
		$result = $DamerauLevenshtein->getRelativeDistance();
		$expected = 0.125;
		$this->assertEquals($expected, $result, '', $delta);

		$firstString = 'ven';
		$secondString = 'Ven';

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
		$result = $DamerauLevenshtein->getRelativeDistance();
		$expected = 0.66666666666667;
		$this->assertEquals($expected, $result, '', $delta);

		$firstString = 'enV';
		$secondString = 'Ven';

		$DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
		$result = $DamerauLevenshtein->getRelativeDistance();
		$expected = 0.33333333333333;
		$this->assertEquals($expected, $result, '', $delta);
	}

}

