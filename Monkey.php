<?php
namespace aoc2022;

Class Monkey {
	public $filePath = '/Users/arne/dev/aoc2022/input_11/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_11/input_test.txt';
	public $lines;
	public $monkeys = [];

	public function __construct(string $test)
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);
		$this->lines = array_filter($this->lines);

		$stuff = array_chunk($this->lines, 6);

		foreach ($stuff as $key => &$arr) {
			preg_match_all('/ (\d\d)/', $arr[1], $matches);
			$items = $matches[0];

			preg_match('/: new = (.+)/', $arr[2], $match);
			$opParts = explode(' ', ($match[1]));
			$first = $opParts[0];
			$operand = $opParts[1];
			$second = $opParts[2];

			preg_match('/.+ by (\d+)$/', $arr[3], $match);
			$divider = $match[1];

			preg_match('/.+monkey (\d+)$/', $arr[4], $match);
			$pos = $match[1];

			preg_match('/.+monkey (\d+)$/', $arr[5], $match);
			$neg = $match[1];

			$monkey = [
				'items' => $items,
				'operand' => $operand,
				'first' => $first,
				'second' => $second,
				'pos' => $pos,
				'neg' => $neg,
				'divider' => $divider,
				'count' => 0,
			];

			$this->monkeys[] = $monkey;
		}

		$a = time();
		for ($i=0; $i < 10 ; $i++) { 
			$this->run();
		}

		$res = 1;
		$duo = array_column($this->monkeys, 'count');
		// rsort($duo);
		$res = $duo[0] * $duo[1];
		print_r($duo);

		print_r(PHP_EOL);
		$b = time();
		print_r($b - $a);
		print_r(PHP_EOL);
		// print_r($res);
	}

	public function run()
	{
		foreach ($this->monkeys as &$monkey) {
			foreach ($monkey['items'] as $itKey => $item) {
				// print_r($item . PHP_EOL);
				$a = ($monkey['first'] == 'old') ? $item : $monkey['first'];
				$b = ($monkey['second'] == 'old') ? $item : $monkey['second'];

				print_r($a);
				if ($monkey['operand'] == '+') {
					print_r('+');
					$worry = gmp_add($a, $b);
				} else {
					$worry = gmp_mul($a, $b);
					print_r('*');
				}
				print_r($b);

				// $worry = floor($worry / 3);

				// print_r('worry: ' . $worry . PHP_EOL);

				if (gmp_mod($worry, $monkey['divider']) == 0) {
					print_r('mod ' . $monkey['divider'] . ' is 0');
					$this->monkeys[$monkey['pos']]['items'][] = $worry;
				} else {
					print_r('mod ' . $monkey['divider'] . ' is not 0');
					$this->monkeys[$monkey['neg']]['items'][] = $worry;
				}
				print_r(PHP_EOL);
				unset($monkey['items'][$itKey]);
				$monkey['count']++;
			}
		}
	}
}
