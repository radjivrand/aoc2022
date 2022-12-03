<?php
namespace aoc2022;

Class RockPaperScissors {
	// Opponent: A for Rock, B for Paper, and C for Scissors
	// You: X for Rock, Y for Paper, and Z for Scissors
	// Kaotus: 0, viik on 3, võit on 6
	// 1 for Rock, 2 for Paper, and 3 for Scissors
	public $filePath = '/Users/arne/dev/aoc2022/input_02/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_02/input_test.txt';

	public $games;

	public $reference = [
		'A' => 'kivi',
		'B' => 'paber',
		'C' => 'käärid',
		'X' => 'kivi',
		'Y' => 'paber',
		'Z' => 'käärid',
	];

	public $gameResults = [
		'kivipaber' => 6,
		'kivikäärid' => 0,
		'kivikivi' => 3,
		'paberkäärid' => 6,
		'paberkivi' => 0,
		'paberpaber' => 3,
		'kääridkivi' => 6,
		'kääridpaber' => 0,
		'kääridkäärid' => 3,
	];

	public $myScore = [
		'kivi' => 1,
		'paber' => 2,
		'käärid' => 3,
	];

	public $neededResult = [
		'X' => 0,
		'Y' => 3,
		'Z' => 6,
	];

	public $variants = [
		'kivi' => [
			0 => 'käärid',
			3 => 'kivi',
			6 => 'paber',
		],
		'paber' => [
			0 => 'kivi',
			3 => 'paber',
			6 => 'käärid',
		],
		'käärid' => [
			0 => 'paber',
			3 => 'käärid',
			6 => 'kivi',
		],
	];

	public $sum;

	public function __construct(string $test = '')
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$handle = file($fileName, FILE_IGNORE_NEW_LINES);

		foreach ($handle as $key => &$value) {
			$value = preg_split('/\s/', $value);
			$this->games[] = [
				'vastane' => $this->reference[$value[0]],
				'mina' => $this->reference[$value[1]],
				'neededResult' => $this->neededResult[$value[1]],
			];
		}
	}

	public function getGameScore(array &$game)
	{
		$game['gamescore'] = $this->gameResults[$game['vastane'] . $game['mina']];
		$game['needToThrow'] = $this->variants[$game['vastane']][$game['neededResult']];
	}

	public function getMyScore(array &$game)
	{
		$game['myscore'] = $this->myScore[$game['mina']];
		$game['myOtherScore'] = $this->myScore[$game['needToThrow']];
	}

	public function calculateScores()
	{
		foreach ($this->games as &$game) {
			$this->getGameScore($game);
			$this->getMyScore($game);
		}
	}

	public function sumScores()
	{
		foreach ($this->games as $game) {
			$this->sum += $game['neededResult'];
			$this->sum += $game['myOtherScore'];
		}
	}
}





















