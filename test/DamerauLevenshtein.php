<?php
require('../DamerauLevenshtein.php');


$names = array(
	'Niall' => 'Niall',
	'O\'Callaghan' => 'OCallaghan'
);

foreach($names as $edexName => $appName) {
	$dl = new DamerauLevenshtein($edexName, $appName, 1, 1, 1, 1);
	echo "Distance (" . $edexName . " => " . $appName . "): " . $dl->getSimilarity() . "\n";
}

/*
$dl = new DamerauLevenshtein('dk gry', 'dark grey',  1,6,6,1);
echo "Edit distance: ";
var_dump($dl->getSimilarity());

echo "Similarity, relative: ";
var_dump($dl->getRelativeDistance());

echo "Maximum possible edit distance: ";
var_dump($dl->getMaximalDistance());

echo "Matrix: \n";
echo $dl->displayMatrix() . "\n";
*/

