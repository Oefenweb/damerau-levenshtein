<?php
namespace Oefenweb\DamerauLevenshtein\Test;

use Oefenweb\DamerauLevenshtein\DamerauLevenshtein;
use PHPUnit\Framework\TestCase;

class DamerauLevenshteinTest extends TestCase
{
    /**
     * Data provider for `getSimilarity`.
     *
     * @return array
     */
    public function getSimilarityProvider(): array
    {
        return [
            ['foo', 'foo', 0],
            ['foo', 'fooo', 1],
            ['foo', 'bar', 3],

            ['123', '12', 1],
            ['qwe', 'qwa', 1],
            ['awe', 'qwe', 1],
            ['фыв', 'фыа', 1],
            ['vvvqw', 'vvvwq', 1],
            ['qw', 'wq', 1],
            ['qq', 'ww', 2],
            ['qw', 'qw', 0],
            ['пионер', 'плеер', 3],
            ['пионер', 'пионеер', 1],
            ['пионер', 'поинер', 1],
            ['pioner', 'poner', 1],
            ['пионер', 'понер', 1],
        ];
    }

    /**
     * Tests `getSimilarity`.
     *
     * @param string $firstString
     * @param string $secondString
     * @param int $expected
     * @return void
     * @dataProvider getSimilarityProvider
     */
    public function testGetSimilarity(string $firstString, string $secondString, int $expected): void
    {
        $DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
        $actual = $DamerauLevenshtein->getSimilarity();

        $this->assertSame($expected, $actual);
    }

    /**
     * Tests `getInsCost`.
     *
     * @return void
     */
    public function testGetInsCost(): void
    {
        list($firstString, $secondString) = $this->getDefaultStrings();
        list($insCost, $delCost, $subCost, $transCost) = $this->getDefaultCosts();

        // Default insert cost

        $DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
        $actual = $DamerauLevenshtein->getInsCost();
        $expected = $insCost;

        $this->assertSame($expected, $actual);

        // Non-default insert cost

        $insCost = 2;

        $DamerauLevenshtein = new DamerauLevenshtein(
            $firstString,
            $secondString,
            $insCost,
            $delCost,
            $subCost,
            $transCost
        );
        $actual = $DamerauLevenshtein->getInsCost();
        $expected = $insCost;

        $this->assertSame($expected, $actual);
    }

    /**
     * Tests `setInsCost`.
     *
     * @param int $cost
     * @return void
     * @dataProvider setXCostProvider
     */
    public function testSetInsCost(int $cost): void
    {
        list($firstString, $secondString) = $this->getDefaultStrings();

        $DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
        $DamerauLevenshtein->setInsCost($cost);
        $this->assertSame($cost, $DamerauLevenshtein->getInsCost());
    }

    /**
     * Tests `getDelCost`.
     *
     * @return void
     */
    public function testGetDelCost(): void
    {
        list($firstString, $secondString) = $this->getDefaultStrings();
        list($insCost, $delCost, $subCost, $transCost) = $this->getDefaultCosts();

        // Default delete cost

        $DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
        $actual = $DamerauLevenshtein->getDelCost();
        $expected = $delCost;

        $this->assertSame($expected, $actual);

        // Non-default delete cost

        $delCost = 2;

        $DamerauLevenshtein = new DamerauLevenshtein(
            $firstString,
            $secondString,
            $insCost,
            $delCost,
            $subCost,
            $transCost
        );
        $actual = $DamerauLevenshtein->getDelCost();
        $expected = $delCost;

        $this->assertSame($expected, $actual);
    }

    /**
     * Data provider for `set<x>Cost`.
     *
     * @return array
     */
    public function setXCostProvider(): array
    {
        return [
            [1],
            [2],
        ];
    }

    /**
     * Tests `setDelCost`.
     *
     * @param int $cost
     * @return void
     * @dataProvider setXCostProvider
     */
    public function testSetDelCost(int $cost): void
    {
        list($firstString, $secondString) = $this->getDefaultStrings();

        $DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
        $DamerauLevenshtein->setDelCost($cost);
        $this->assertSame($cost, $DamerauLevenshtein->getDelCost());
    }

    /**
     * Tests `getSubCost`.
     *
     * @return void
     */
    public function testGetSubCost(): void
    {
        list($firstString, $secondString) = $this->getDefaultStrings();
        list($insCost, $delCost, $subCost, $transCost) = $this->getDefaultCosts();

        // Default substitution cost

        $DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
        $actual = $DamerauLevenshtein->getSubCost();
        $expected = $subCost;

        $this->assertSame($expected, $actual);

        // Non-default substitution cost

        $subCost = 2;

        $DamerauLevenshtein = new DamerauLevenshtein(
            $firstString,
            $secondString,
            $insCost,
            $delCost,
            $subCost,
            $transCost
        );
        $actual = $DamerauLevenshtein->getSubCost();
        $expected = $subCost;

        $this->assertSame($expected, $actual);
    }

    /**
     * Tests `setSubCost`.
     *
     * @param int $cost
     * @return void
     * @dataProvider setXCostProvider
     */
    public function testSetSubCost(int $cost): void
    {
        list($firstString, $secondString) = $this->getDefaultStrings();

        $DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
        $DamerauLevenshtein->setSubCost($cost);
        $this->assertSame($cost, $DamerauLevenshtein->getSubCost());
    }

    /**
     * Tests `getTransCost`.
     *
     * @return void
     */
    public function testGetTransCost(): void
    {
        list($firstString, $secondString) = $this->getDefaultStrings();
        list($insCost, $delCost, $subCost, $transCost) = $this->getDefaultCosts();

        // Default transposition cost

        $DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
        $actual = $DamerauLevenshtein->getTransCost();
        $expected = $transCost;

        $this->assertSame($expected, $actual);

        // Non-default transposition cost

        $transCost = 2;

        $DamerauLevenshtein = new DamerauLevenshtein(
            $firstString,
            $secondString,
            $insCost,
            $delCost,
            $subCost,
            $transCost
        );
        $actual = $DamerauLevenshtein->getTransCost();
        $expected = $transCost;

        $this->assertSame($expected, $actual);
    }

    /**
     * Tests `setTransCost`.
     *
     * @param int $cost
     * @return void
     * @dataProvider setXCostProvider
     */
    public function testSetTransCost(int $cost): void
    {
        list($firstString, $secondString) = $this->getDefaultStrings();

        $DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
        $DamerauLevenshtein->setTransCost($cost);
        $this->assertSame($cost, $DamerauLevenshtein->getTransCost());
    }

    /**
     * Data provider for `getRelativeDistance`.
     *
     * @return array
     */
    public function getRelativeDistanceProvider(): array
    {
        return [
            ['O\'Callaghan', 'OCallaghan', 0.90909090909091],
            ['Thom', 'Mira', 0.0],
            ['Oldeboom', 'Ven', 0.125],
            ['ven', 'Ven', 0.66666666666667],
            ['enV', 'Ven', 0.3333333333333],
        ];
    }

    /**
     * Tests `getRelativeDistance`.
     *
     * @param string $firstString
     * @param string $secondString
     * @param float $expected
     * @return void
     * @dataProvider getRelativeDistanceProvider
     */
    public function testGetRelativeDistance(string $firstString, string $secondString, float $expected): void
    {
        $delta = pow(10, -4);

        $DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
        $actual = $DamerauLevenshtein->getRelativeDistance();
        $this->assertEquals($expected, $actual, '', $delta);
    }

    /**
     * Tests `getMatrix`.
     *
     * @return void
     */
    public function testGetMatrix(): void
    {
        list($firstString, $secondString) = $this->getDefaultStrings();

        $DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
        $actual = $DamerauLevenshtein->getMatrix();
        $expected = [
            [0, 1, 2, 3],
            [1, 1, 2, 3],
            [2, 2, 2, 3],
            [3, 3, 3, 3]
        ];
        $this->assertSame($expected, $actual);
    }

    /**
     * Tests `displayMatrix`.
     *
     * @return void
     */
    public function testDisplayMatrix(): void
    {
        list($firstString, $secondString) = $this->getDefaultStrings();

        $DamerauLevenshtein = new DamerauLevenshtein($firstString, $secondString);
        $actual = $DamerauLevenshtein->displayMatrix();
        $expected = implode('', [
            "  foo\n",
            " 0123\n",
            "b1123\n",
            "a2223\n",
            "r3333\n",
        ]);
        $this->assertSame($expected, $actual);
    }

    /**
     * Returns the default costs.
     *
     * @return array Costs (insert, delete, substitution, transposition)
     */
    protected function getDefaultCosts(): array
    {
        $insCost = 1;
        $delCost = 1;
        $subCost = 1;
        $transCost = 1;

        return [$insCost, $delCost, $subCost, $transCost];
    }

    /**
     * Returns the default strings.
     *
     * @return array Strings (foo, bar)
     */
    protected function getDefaultStrings(): array
    {
        $firstString = 'foo';
        $secondString = 'bar';

        return [$firstString, $secondString];
    }
}
