<?php
namespace aoc2022;

Class Calorie {
	public $elves;

	public $filePath = '/Users/arne/dev/aoc2022/input_01/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_01/input_test.txt';

	public function __construct(string $test = '')
		{
			$fileName = $test == '' ? $this->filePath : $this->testPath;
			$handle = file($fileName, FILE_IGNORE_NEW_LINES);
			array_push($handle, []);
		
			$elf = [];
			foreach ($handle as $row) {
				if ($row) {
					$elf[] = $row;
				} else {
					$this->elves[] = $elf;
					$elf = [];
				}
			}

		}	

		public function getSumForElf($value='')
		{
			foreach ($this->elves as &$elf) {
				$sum = 0;
				foreach ($elf as $value) {
					$sum += $value;
				}

				$elf['max'] = $sum;
			}
			print_r($this->elves);
		}

		public function getTotal(int $numberOfElves = 1)
		{
			$masterSum = 0;

			$sums = array_column($this->elves, 'max');

			arsort($sums);

			$counter = 0;
			foreach ($sums as $sum) {
				if ($counter > $numberOfElves) {
					break;
				}
				$masterSum += $sum;
				$counter++;
			}

			return $masterSum;
		}
}