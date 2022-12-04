<?php
namespace aoc2022;

Class RuckSack {
	public $filePath = '/Users/arne/dev/aoc2022/input_03/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_03/input_test.txt';

	public $lines;
	public $triples;
	public $score;

	public function __construct(string $test = '')
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);
		$this->triples = array_chunk($this->lines, 3);
	}

	public function splitInHalf(string $content)
	{
		$contArr = str_split($content);
		$res = array_chunk($contArr, count($contArr)/2);
		return $res;
	}

	public function findSingleLetter(array $arr)
	{
		$firstMirr = array_flip($arr[0]);
		$secondMirr = array_flip($arr[1]);

		foreach ($firstMirr as $key => $value) {
			if (isset($secondMirr[$key])) {
				return $key;
				break;				
			}
		}
	}

	public function getPriority(string $letter)
	{
		$priority = ord($letter) - 96;
		return $priority > 0 ? $priority : $priority + 58;
	}

	public function addPriorities()
	{
		foreach ($this->lines as $line) {
			$this->score += $this->getPriority($this->findSingleLetter($this->splitInHalf($line)));
		}
	}

	public function findWithRegex()
	{
		$lettersFromTrips = [];

		foreach ($this->triples as $triple) {
			preg_match_all('/[' . $triple[0] . ']/', $triple[1], $matches);
			$union = implode('', $matches[0]);
			preg_match_all('/[' . $union . ']/', $triple[2], $matches);
			$matches = array_unique($matches[0]);
			$lettersFromTrips[] = $matches[0];
		}


		foreach ($lettersFromTrips as $value) {
			$this->score += $this->getPriority($value);
		}
	}
}