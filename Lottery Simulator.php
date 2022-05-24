<?php

$number_of_draws = 50000000;

$lucky_dip = true;
$selected_numbers = ["main" => [9, 42, 3, 17, 25], "stars" => [2, 6]];
$random_numbers = ["main" => randomGen(1, 50, 5), "stars" => randomGen(1, 12, 2)];
$numbers = $lucky_dip ? $random_numbers : $selected_numbers;
$cost_per_entry = 250;

$total_winnings = 0;
$draws_run = 0;
$draws_won = 0;
$draws_lost = 0;


function randomGen($min, $max, $quantity)
{
    $numbers = range($min, $max);
    shuffle($numbers);
    return array_slice($numbers, 0, $quantity);
}

function runDraw($numbers)
{
    $drawn_numbers = ["main" => randomGen(1, 50, 5), "stars" => randomGen(1, 12, 2)];

    $array_diff = [
        "main" => array_diff($numbers['main'], $drawn_numbers['main']),
        "stars" => array_diff($numbers['stars'], $drawn_numbers['stars'])
    ];

    $matches = [
        "main_matches" => 5 - count($array_diff['main']),
        "star_matches" => 2 - count($array_diff['stars'])
    ];

    return $matches;
}

function calcWinnings($matches)
{
    $prizes = [
        [0, 0, 0],
        [0, 0, 430],
        [250, 360, 910],
        [600, 730, 3730],
        [2560, 7780, 84470],
        [1356120, 13055430, 4400000000]
    ];

    return $prizes[$matches['main_matches']][$matches['star_matches']];
}

for ($i = 0; $i < $number_of_draws; $i++) {
    $matches = runDraw($numbers);
    $winnings = calcWinnings($matches);

    $draws_run++;
    if ($winnings > 0) {
        $total_winnings += $winnings;
        $draws_won++;
    } else {
        $draws_lost++;
    }
}

$adjusted_total_winnings = $total_winnings / 100;
$total_spend = $cost_per_entry / 100 * $number_of_draws;
$net_profit = $adjusted_total_winnings - $total_spend;

$fmt = new NumberFormatter('en_GB', NumberFormatter::CURRENCY);

print_r([
    "numbers run" => implode(", ", $numbers['main']) . " [" . implode(", ", $numbers['stars']) . "]",
    "draws_run" => $draws_run,
    "draws_won" => $draws_won,
    "draws_lost" => $draws_lost,
    "total_spent" => $fmt->formatCurrency($total_spend, 'GBP'),
    "total winnings" => $fmt->formatCurrency($adjusted_total_winnings, 'GBP'),
    "net profit" => $fmt->formatCurrency($adjusted_total_winnings - $total_spend, 'GBP')
]);