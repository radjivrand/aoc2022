<?php
namespace aoc2022;

Class Radio {
	public $filePath = '/Users/arne/dev/aoc2022/input_06/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_06/input_test.txt';

	public $lines;

	public function __construct(string $test)
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);
	}

	public function findMarker($length)
	{
		$arr = str_split($this->lines[0]);
		$counter = 0;

		do {
			$slice = array_slice($arr, $counter, $length);
			$counter++;
		} while (count(array_flip($slice)) != $length);

		return $counter + $length - 1;
	}
}