<?php
require '../src/DamerauLevenshtein.php';

$names = array(
	'Niall' => 'Niall',
	'O\'Callaghan' => 'OCallaghan'
);

foreach ($names as $edexName => $appName) {
	$dl = new DamerauLevenshtein($edexName, $appName, 1, 1, 1, 1);
	echo "Distance (" . $edexName . " => " . $appName . "): " . $dl->getSimilarity() . "\n";
}
