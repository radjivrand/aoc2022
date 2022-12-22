<?php
namespace aoc2022;

ini_set('log_errors', 1);

spl_autoload_register(function ($className) {
    require '/Users/arne/dev/' . str_replace('\\', '/', $className) . '.php';
});

error_reporting(-1);

$runMode = $argv[1] ?? '';

// $r = new Register($runMode);
// $rope = new Rope($runMode);
$snake = new Snake($runMode);
// $m = new Monkey($runMode);


// echo PHP_EOL;
// echo PHP_EOL;
// echo '##################' . PHP_EOL;
// echo '###  AoC 2022  ###' . PHP_EOL;
// echo '##################' . PHP_EOL;

echo PHP_EOL;

// ex 1
// $cal = new Calorie($runMode);
// $cal->getSumForElf();
// $res = $cal->getTotal(2);

// ex 2
// $rps = new RockPaperScissors();
// $rps->calculateScores();
// $rps->sumScores();
// print_r($rps->sum);

// ex 3
// $rs = new RuckSack();
// // $letter = $rs->addPriorities();
// $letter = $rs->findWithRegex();
// print_r('result: ' . $rs->score);

// ex4
// $c = new Cleaner($runMode);
// print_r($c->countDupes('Partial'));

// ex 5
// $crane = new Crane($runMode);
// $crane->output();
// $crane->work();
// $crane->workMore();
// print_r($crane->res);

// ex 6
// $r = new Radio($runMode);
// print_r($r->findMarker(14));

// ex 7
// $sorter = new Sorter($runMode);

// ex 8
// $tree = new Tree($runMode);

