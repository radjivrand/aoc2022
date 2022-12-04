<?php
namespace aoc2022;

Class Cleaner {
	public $filePath = '/Users/arne/dev/aoc2022/input_04/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_04/input_test.txt';

	public $lines;
	public $pairs;

	public function __construct(string $test)
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

		foreach ($this->lines as $line) {
			$this->pairs[] = explode(',', $line);
		}
	}

	public function checkForFullOverlap(array $arr)
	{
		$firstTeamArr = explode('-', $arr[0]);
		$secondTeamArr = explode('-', $arr[1]);

		if (
			($firstTeamArr[0] <=$secondTeamArr[0]
			&& $firstTeamArr[1] >= $secondTeamArr[1])
			|| ($secondTeamArr[0] <=$firstTeamArr[0]
			&& $secondTeamArr[1] >= $firstTeamArr[1])
		) {
			return true;
		}

		return false;
	}

	public function checkForPartialOverlap(array $arr)
	{
		$first = explode('-', $arr[0]);
		$second = explode('-', $arr[1]);

		$firstArr = [];
		for ($i=$first[0]; $i <= $first[1]; $i++) { 
			$firstArr[] = $i;
		}

		$secondArr = [];
		for ($i=$second[0]; $i <= $second[1]; $i++) { 
			$secondArr[] = $i;
		}

		$intersectArr = array_intersect($firstArr, $secondArr);

		return count($intersectArr) > 0;
	}

	public function countDupes(string $mode = 'Full')
	{
		$counter = 0;
		foreach ($this->pairs as $pair) {
			$counter += $this->{'checkFor' . $mode . 'Overlap'}($pair) ? 1 : 0;

		}
		return $counter;
	}
}