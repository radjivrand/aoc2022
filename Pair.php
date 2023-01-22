<?php
namespace aoc2022;

Class Pair {
	const FILE_PATH = '/Users/arne/dev/aoc2022/input_13/input.txt';
	const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_13/input_test.txt';

	protected $indexCounter;

	//4907 is too high
	//4843 is too high

	public function __construct(string $test)
	{
		$fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

		foreach ($this->lines as $key => $value) {
			if (empty($value)) {
				unset($this->lines[$key]);
			}
		}

		$this->lines = array_chunk($this->lines, 2);

		$this->run();
		// print_r($this->lines);
	}

	public function run()
	{
		foreach ($this->lines as $index => $line) {
			if ($this->compare(json_decode($line[0]), json_decode($line[1]))) {
				$this->indexCounter += $index + 1;
				// print_r(['true']);
			} else {
				// print_r(['false']);
			}
		}


		// foreach ($this->lines as $index => $pairs) {
		// 	print_r($index . PHP_EOL);
		// 	$left = json_decode($pairs[0]);
		// 	$right = json_decode($pairs[1]);

		// 	if ($this->compare($left, $right)) {
		// 		$this->indexCounter += $index;
		// 	} else {
		// 	}
		// }

		print_r($this->indexCounter);
	}

	public static function reduce($a, $b)
	{
		foreach (range(0, count($a) -1) as $value) {
			if ((isset($a[$value]) && isset($b[$value])) && $a[$value] == $b[$value]) {
				unset($a[$value]);
				unset($b[$value]);
			}
		}

		return [array_values($a), array_values($b)];
	}

	public static function compare($a, $b)
	{
		if (gettype($a) != gettype($b)) {
			if (is_array($a)) {
				$b = [$b];
			} else {
				$a = [$a];
			}
			
			return self::compare($a, $b);
		}

		$reduced = self::reduce($a, $b);

		$a = $reduced[0];
		$b = $reduced[1];

		if (empty($a)) {
			return true;
		}
	
		if (empty($b)) {
			return false;
		}

		// eksib ilmselt kuskil siin
		if (is_array($a) && is_array($b)) {
			foreach (range(0, count($a) -1) as $key) {
				if ($a[$key] != $b[$key] && (is_int($a[$key]) && is_int($b[$key]))) {
					return $a[$key] < $b[$key];
				} else {
					return self::compare($a[$key], $b[$key]);
				}
			}
		}

		if (
			is_array($a)
			&& is_array($b)
			&& count($a) == count($b)
			&& count($a) == 1
		) {
			return self::compare($a[0], $b[0]);
		}
	}
}
