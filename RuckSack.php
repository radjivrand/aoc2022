<?php
namespace aoc2022;

Class RuckSack {
	public $filePath = '/Users/arne/dev/aoc2022/input_01/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_01/input_test.txt';

	public function __construct(string $test = '')
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$handle = file($fileName, FILE_IGNORE_NEW_LINES);
		array_push($handle, []);
	}	
}