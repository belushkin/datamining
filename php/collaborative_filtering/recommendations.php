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

// Jaccard index
function sim_jaccard($prefs, $person1, $person2)
{
    $result1 = array();
    foreach ($prefs[$person1] as $film => $score) {
	$result1[] = $score;
    }

    $result2 = array();
    foreach ($prefs[$person2] as $film => $score) {
	$result2[] = $score;
    }

    $arr_intersection	= array_intersect($result1, $result2);
    $arr_union		= array_merge($result1, $result2);
    $coefficient	= count( $arr_intersection ) / count( $arr_union );

    return $coefficient;
}

echo "Euclid distance:     ", sim_distance($critics, 'Lisa Rose', 'Gene Seymour'), "\n";
echo "Pearson correlation: ", sim_pearson($critics, 'Lisa Rose', 'Gene Seymour'), "\n";
echo "Tanimoto coefficient: ", sim_tanimoto($critics, 'Lisa Rose', 'Gene Seymour'), "\n";
echo "Jaccard index: ", sim_jaccard($critics, 'Lisa Rose', 'Gene Seymour'), "\n";

function topMatches($prefs, $person, $n = 5, $similarity = 'sim_pearson') 
{
    $scores = array();
    foreach ($prefs as $other => $films) {
	if ($other != $person) {
	    $scores[$other] = $similarity($prefs, $person, $other);
	}
    }
    asort($scores);
    return array_reverse($scores);
}

print_r(topMatches($critics, 'Toby', $n = 3)); echo "\n";

function getRecommendations($prefs, $person, $similarity = 'sim_pearson') 
{
    $totals = array();
    $simSum = array();
    foreach ($prefs as $other => $films) {
	if ($other != $person) {
	    $sim = $similarity($prefs, $person, $other);
	    if ($sim <= 0) {
		continue;
	    }
    	    foreach ($prefs[$other] as $film => $score) {
		if (!isset($prefs[$person][$film]) || $prefs[$person][$film] == 0) {
		    @$totals[$film] += $score * $sim;
		    @$simSum[$film] += $sim;
		}
            }
	}
    }
    foreach ($totals as $film => $total) {
	$rankings[$film] = $total/$simSum[$film];
    }
    asort($rankings);
    return array_reverse($rankings);
}

print_r(getRecommendations($critics, 'Toby'));





