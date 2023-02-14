<?php
namespace aoc2022;

ini_set('log_errors', 1);

spl_autoload_register(function ($className) {
    require '/Users/arne/dev/' . str_replace('\\', '/', $className) . '.php';
});

error_reporting(-1);

$runMode = $argv[1] ?? '';

// $maze = new Dijk($runMode);
// $b = new Manhattan($runMode);
// $cube = new Cubes($runMode);
$cube = new Tetris($runMode);


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

// ex 9
// $rope = new Rope($runMode);
// $snake = new Snake($runMode);

// ex 10
// $r = new Register($runMode);
// ex 11
// $m = new Monkey($runMode);

// ex13
// $pair = new Opener($runMode);
// $pair = new Pair($runMode); // fail

// ex 14
// $sand = new Hourglass($runMode);

// $ex15
// first part
// $b = new Manhattan($runMode);

// ex 21
// $yell = new Yell($runMode);