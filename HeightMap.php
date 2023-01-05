<?php
namespace aoc2022;

Class HeightMap {
	public $filePath = '/Users/arne/dev/aoc2022/input_12/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_12/input_test.txt';
	public $lines;
	public $visited;
	public $correct;
	public $startX;
	public $startY;
	public $endX;
	public $endY;

	public function __construct(string $test)
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);
		// $this->lines = array_filter($this->lines);
		foreach ($this->lines as &$line) {
			$line = str_split($line);
		}

		$start = $this->getPosition('S');
		$this->startX = $start[0];
		$this->startY = $start[1];

		$this->lines[$this->startX][$this->startY] = '`';

		$end = $this->getPosition('E');
		$this->endX = $end[0];
		$this->endY = $end[1];
		// $this->lines[$this->endX][$this->endY] = '{';

		$this->solve();
		$this->output();
		print_r($this->correct);

		$this->correctOutput();
		// print_r($this->lines);
	}

	public function solve()
	{
		foreach ($this->lines as $rowIndex => $row) {
			foreach ($row as $colIndex => $value) {
				$this->visited[$rowIndex][$colIndex] = false;
				$this->correct[$rowIndex][$colIndex] = false;
			}
		}

		$this->search($this->startX, $this->startY, '`');
	}

	public function search($x, $y, $previous)
	{
		// print_r('search for: ' . $x . ', ' . $y . ', ' . $previous . PHP_EOL);
		// print_r(ord($this->lines[$x][$y]));
		// print_r(PHP_EOL);
		// print_r(ord($previous));
		// print_r(PHP_EOL);
		// print_r([$x, $y, $previous, $this->lines[$x][$y]]);

		// if ($x == $this->endX && $y == $this->endY) {
		if ($this->lines[$x][$y] == 'E') {
			// print_r('happycase');
			// print_r(PHP_EOL);
			return true;
		}

		if (ord($this->lines[$x][$y]) - ord($previous) > 1 || $this->visited[$x][$y] === true) {
			// print_r('wall or visited');
			// print_r(PHP_EOL);
			return false;
		}

		$this->visited[$x][$y] = true;

		if ($x != 0 && $this->search($x - 1, $y, $this->lines[$x][$y])) {
			$this->correct[$x][$y] = '<';
			// print_r('3');
			// print_r(PHP_EOL);
			return true;
		}

		if ($x != (count($this->lines) - 1) && $this->search($x + 1, $y, $this->lines[$x][$y])) {
			$this->correct[$x][$y] = '>';
			// print_r('4');
			// print_r(PHP_EOL);
			return true;
		}

		if ($y != 0 && $this->search($x, $y - 1, $this->lines[$x][$y])) {
			$this->correct[$x][$y] = '^';
			// print_r('5');
			// print_r(PHP_EOL);
			return true;
		}

		if ($y != (count($this->lines[0]) - 1)  && $this->search($x, $y + 1, $this->lines[$x][$y])) {
			$this->correct[$x][$y] = 'v';
			// print_r('6');
			// print_r(PHP_EOL);
			return true;
		}

		// print_r('7');
		// print_r(PHP_EOL);
		return false;
	}


	public function output()
	{
		print_r(PHP_EOL);
		foreach ($this->lines as $yKey => $line) {
			foreach ($line as $xKey => $value) {
				print_r($value);
			}
			print_r(PHP_EOL);
		}
	}

	public function correctOutput()
	{
		print_r(PHP_EOL);
		foreach (range(0, 4) as $xKey) {
			foreach (range(0, 7) as $yKey) {
				if ($this->correct[$xKey][$yKey] !== false) {

					print_r($this->correct[$xKey][$yKey]);
				} else {
					print_r('.');
				}
			}
			print_r(PHP_EOL);
		}
	}

	public function getPosition($marker)
	{
		foreach ($this->lines as $rowIndex => $row) {
			foreach ($row as $colIndex => $value) {
				if ($value == $marker) {
					return [$rowIndex, $colIndex];
				}
			}
		}
	}
}
