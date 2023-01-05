<?php
namespace aoc2022;

Class Monkey {
	public $filePath = '/Users/arne/dev/aoc2022/input_11/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_11/input_test.txt';
	public $lines;
	public $monkeys = [];
	public $prod = 1;

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

		$dividers = array_column($this->monkeys, 'divider');
		foreach ($dividers as $key => $value) {
			$this->prod *= $value;
		}

		$start = microtime(true);

		for ($i=0; $i < 10000 ; $i++) { 
			$this->run();
		}

		$end = microtime(true);
		print_r(($end - $start));
		print_r(PHP_EOL);


		$res = 1;
		$duo = array_column($this->monkeys, 'count');
		rsort($duo);
		$res = gmp_mul($duo[0], $duo[1]);
		print_r($res);

	}

	public function applyDividers($number)
	{
		$modulo = $number % $this->prod;
		$times = ($number - $modulo) / $this->prod;

		$number -= $this->prod * $times;

		return $number;
	}

	public function run()
	{
		foreach ($this->monkeys as &$monkey) {
			foreach ($monkey['items'] as $itKey => $item) {
				$a = ($monkey['first'] == 'old') ? $item : $monkey['first'];
				$b = ($monkey['second'] == 'old') ? $item : $monkey['second'];

				if ($monkey['operand'] == '+') {
					$worry = $a + $b;
				} else {
					$worry = $a * $b;
				}

				if (gmp_mod($worry, $monkey['divider']) == 0) {
					$this->monkeys[$monkey['pos']]['items'][] = $this->applyDividers($worry);
				} else {
					$this->monkeys[$monkey['neg']]['items'][] = $this->applyDividers($worry);
				}

				unset($monkey['items'][$itKey]);
				$monkey['count']++;
			}
		}
	}
}
