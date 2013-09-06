<?php

require_once('critics.php');

// Euclid distance
function sim_distance($prefs, $person1, $person2)
{
    $sumOfSquares = 0;
    foreach ($prefs[$person1] as $film => $score) {
        if (isset($prefs[$person2][$film])) {
            $sumOfSquares += pow($prefs[$person1][$film] - $prefs[$person2][$film], 2);
        }
    }
    return 1 / (1 + $sumOfSquares);
}

// Pearson correlation
function sim_pearson($prefs, $person1, $person2)
{
    $result = array();
    foreach ($prefs[$person1] as $film => $score) {
        if (isset($prefs[$person2][$film])) {
            $result[] = $film;
        }
    }

    $sum1 = $sum2 = $sum1Sq = $sum2Sq = $pSum = 0;
    $n = count($result);

    foreach ($result as $film) {
        $sum1 += $prefs[$person1][$film];
    }
    foreach ($result as $film) {
        $sum2 += $prefs[$person2][$film];
    }

    foreach ($result as $film) {
        $sum1Sq += pow($prefs[$person1][$film], 2);
    }
    foreach ($result as $film) {
        $sum2Sq += pow($prefs[$person2][$film], 2);
    }

    foreach ($result as $film) {
        $pSum += ($prefs[$person1][$film] * $prefs[$person2][$film]);
    }

    $num = $pSum - ($sum1 * $sum2/$n);
    $den = sqrt(($sum1Sq - pow($sum1, 2)/$n) * ($sum2Sq - pow($sum2, 2)/$n));
    if ($den == 0) {
        return 0;
    }
    return $num / $den;
}

// Tanimoto coefficient
function sim_tanimoto($prefs, $person1, $person2)
{
    $result = array();
    foreach ($prefs[$person1] as $film => $score) {
        if (isset($prefs[$person2][$film]) && $prefs[$person1][$film] == $prefs[$person2][$film]) {
            $result[] = $film;
        }
    }
    $Nc = count($result);
    $Na = count($prefs[$person1]);
    $Nb = count($prefs[$person2]);

    return $Nc/($Na + $Nb - $Nc);
}

echo "Euclid distance:     ", sim_distance($critics, 'Lisa Rose', 'Gene Seymour'), "\n";
echo "Pearson correlation: ", sim_pearson($critics, 'Lisa Rose', 'Gene Seymour'), "\n";
echo "Tanimoto coefficient: ", sim_tanimoto($critics, 'Lisa Rose', 'Gene Seymour'), "\n";
