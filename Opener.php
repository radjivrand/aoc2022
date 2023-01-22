<?php
namespace aoc2022;

use aoc2022\Pair;

Class Opener {
	const FILE_PATH = '/Users/arne/dev/aoc2022/input_13/input.txt';
	const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_13/input_test.txt';

	protected $indexCounter;

	public function __construct(string $test)
	{
		$fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

		foreach ($this->lines as $key => $value) {
			if (empty($value)) {
				unset($this->lines[$key]);
			}
		}

		$this->rows = $this->lines;
		$firstMarker = '[[2]]';
		$secondMarker = '[[6]]';
		$this->rows[] = $firstMarker;
		$this->rows[] = $secondMarker;

		$this->lines = array_chunk($this->lines, 2);

		// $this->run();
		$this->sort();

		$firstIndex = array_search($firstMarker, $this->rows);
		$secondIndex = array_search($secondMarker, $this->rows);

		print_r(($firstIndex + 1) * ($secondIndex + 1));
	}

	public function sort()
	{
		usort($this->rows, function ($a, $b) {
			$res = self::compare(json_decode($a), json_decode($b));

			if ($res === null) {
				return 0;
			}

			return $res ? -1 : 1;
		});
	}

	public function run()
	{
		foreach ($this->lines as $index => $line) {
			$left = json_decode($line[0]);
			$right = json_decode($line[1]);

			if (self::compare($left, $right)) {
				$this->indexCounter += $index + 1;
			}
		}

		print_r($this->indexCounter);
	}

	public function compare($a, $b)
	{
		if (empty($a) && !empty($b)) {
			return true;
		}

		if (!empty($a) && empty($b)) {
			return false;
		}

		if (is_array($a) && is_array($b)) {
			if (count($a) > 1 && count($b) > 1) {
				$aFirst = array_shift($a);
				$bFirst = array_shift($b);			

				if (self::compare($aFirst, $bFirst) === null) {
					return self::compare($a, $b);
				} else {
					return self::compare($aFirst, $bFirst);
				}
			}

			if (count($a) == 1 && count($b) == 1) {
				return self::compare($a[0], $b[0]);
			}

			if ((count($a) > 1 && count($b) == 1) || (count($a) == 1 && count($b) > 1)) {
				if ($a[0] == $b[0]) {
					return count($a) < count($b);
				} else {
					return self::compare($a[0], $b[0]);
				}
			}
		}

		if (is_array($a) && is_int($b)) {
			return self::compare($a, [$b]);
		}

		if (is_array($b) && is_int($a)) {
			return self::compare([$a], $b);
		}

		if (is_int($a) && is_int($b) && $a != $b) {
			return $a < $b;
		}

		return null;
	}
}