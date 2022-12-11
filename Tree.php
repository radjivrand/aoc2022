<?php
namespace aoc2022;

Class Tree {
	public $filePath = '/Users/arne/dev/aoc2022/input_08/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_08/input_test.txt';
	public $lines;
	public $rowAmount;
	public $colAmount;
	public $perimeter;
	public $visible;
	public $scenic = 0;

	public function __construct(string $test)
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

		foreach ($this->lines as &$line) {
			$line = str_split($line);
			if (!isset($this->colAmount)) {
				$this->colAmount = count($line);
			}
		}

		$this->rowAmount = count($this->lines);
		$this->perimeter = 2 * ($this->rowAmount + $this->colAmount) - 4;
		$this->visible = $this->perimeter;
		$this->iterate();
		
		print_r($this->scenic);
	}

	public function getVisibleTrees($arr, $curTreeHeight)
	{
		foreach ($arr as $key => $value) {
			if ($value >= $curTreeHeight) {
				return $key + 1;
			}
		}

		return count($arr);
	}

	public function newScore($r, $c)
	{
		$e = $this->lines[$r][$c];

		$left = array_slice($this->lines[$r], 0, $c);
		$right = array_slice($this->lines[$r], $c + 1, $this->colAmount - 1);
		$up = array_slice(array_column($this->lines, $c), 0, $r);
		$down = array_slice(array_column($this->lines, $c), $r + 1, $this->rowAmount - 1);

		krsort($left);
		krsort($up);

		$left = array_values($left);
		$up = array_values($up);

		$curScore = 1;
		foreach (['left', 'right', 'up', 'down'] as $key => $value) {
			$curScore *= $this->getVisibleTrees($$value, $e);
		}

		return $curScore;
	}


	public function getView($arr, $val)
	{
		$len = count($arr);
		$arr = array_values($arr);

		$max = 0;
		$counter = 0;

		foreach ($arr as $key => $value) {
			if ($value >= $max) {
				$max = $value;
				$counter++;
			} else {
				return $counter;
			}
		}

		return $counter;
	}

	public function getScenicScore($row, $col)
	{
		$left = array_slice($this->lines[$row], 0, $col);
		$right = array_slice($this->lines[$row], $col + 1, $this->colAmount - 1);
		$up = array_slice(array_column($this->lines, $col), 0, $row);
		$down = array_slice(array_column($this->lines, $col), $row + 1, $this->rowAmount - 1);

		krsort($left);
		krsort($up);

		$resLeft = $this->getView($left, $this->lines[$row][$col]);
		$resRight = $this->getView($right, $this->lines[$row][$col]);
		$resUp = $this->getView($up, $this->lines[$row][$col]);
		$resDown = $this->getView($down, $this->lines[$row][$col]);

		return $resLeft * $resRight * $resUp * $resDown;
	}

	public function isVisible($row, $col)
	{
		$left = array_slice($this->lines[$row], 0, $col);
		$right = array_slice($this->lines[$row], $col + 1, $this->colAmount - 1);
		$up = array_slice(array_column($this->lines, $col), 0, $row);
		$down = array_slice(array_column($this->lines, $col), $row + 1, $this->rowAmount - 1);

		$cur = $this->lines[$row][$col];

		if (
			max($left) < $cur
			|| max($right) < $cur
			|| max($up) < $cur
			|| max($down) < $cur
		) {
			return true;
		}

		return false;
	}

	public function iterate()
	{
		foreach ($this->lines as $rowIndex => $row) {
			foreach ($row as $colIndex => $value) {
				if (!$this->isOnEdge($rowIndex, $colIndex) && $this->isVisible($rowIndex, $colIndex)) {
					$this->visible++;
				}

				if (!$this->isOnEdge($rowIndex, $colIndex)) {
					$score = $this->newScore($rowIndex, $colIndex);
					if ($score > $this->scenic) {
						// print_r($score . PHP_EOL);
						$this->scenic = $score;
					}
				}
			}
		}

	}

	public function isOnEdge($row, $col)
	{
		if (
			$row == 0
			|| $col == 0
			|| $row == $this->rowAmount - 1
			|| $col == $this->colAmount - 1
		) {
			return true;
		}
		return false;
	}
}