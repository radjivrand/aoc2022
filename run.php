<?php
namespace aoc2022;

ini_set('log_errors', 1);

spl_autoload_register(function ($className) {
    require '/Users/arne/dev/' . str_replace('\\', '/', $className) . '.php';
});

error_reporting(E_ERROR | E_PARSE | E_WARNING);

$rs = new RuckSack();

echo PHP_EOL;
echo PHP_EOL;
echo '##################' . PHP_EOL;
echo '###  AoC 2022  ###' . PHP_EOL;
echo '##################' . PHP_EOL;


echo PHP_EOL;



// ex 1
// $cal = new Calorie();
// $cal->getSumForElf();
// $res = $cal->getTotal(2);

// ex 2
// $rps = new RockPaperScissors();
// $rps->calculateScores();
// $rps->sumScores();
// print_r($rps->sum);