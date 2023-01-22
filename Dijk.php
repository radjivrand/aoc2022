<?php
namespace aoc2022;

Class Dijk {
	const FILE_PATH = '/Users/arne/dev/aoc2022/input_12/input.txt';
	const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_12/input_test.txt';
	// const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_12/maze.txt';
	const START_CHAR = 'S';
	const END_CHAR = 'E';
	const WALL_CHAR = '#';

	public $counter = 0;
	public $visited = [];
	public $start;
	public $end;
	public $finished = false;

	public function __construct(string $test)
	{
		$fileName = $test == '' ? Dijk::FILE_PATH : Dijk::TEST_FILE_PATH;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

		foreach ($this->lines as &$line) {
			$line = str_split($line);
		}

		$this->start = $this->findLocation(Dijk::START_CHAR);
		$this->end = $this->findLocation(Dijk::END_CHAR);

		$this->lines[$this->start[0]][$this->start[1]] = '`';
		$this->lines[$this->end[0]][$this->end[1]] = '{';

		$this->printMaze($this->lines);
		$this->visited[] = [...$this->end, $this->counter, '{'];

		while (!$this->finished) {
			$this->counter++;
			$this->solve();
		}

		$map = $this->mapCounters();

		// print_r($this->visited);
		// $this->printMaze($map);

		print_r($this->counter);
	}

	public function mapCounters()
	{
		$arr = $this->lines;
		foreach ($this->visited as $k => $row) {
			$arr[$row[0]][$row[1]] = $row[2];
		}

		return $arr;
	}

	public function solve()
	{
		$newArr = $this->visited;

		foreach ($this->visited as $key => $value) {
			$candidates = $this->getNeighbours($value);
			$current = $value[3];
			$current = $this->lines[$value[0]][$value[1]];
			foreach ($candidates as $k => $cell) {
				if ($this->isWall($cell, $current) || $this->isVisited($cell) || $this->finished) {
					continue;
				}

				if ($cell[0] == $this->start[0] && $cell[1] == $this->start[1]) {
					$this->finished = true;
				}

				$newArr[] = $cell;
			}
		}

		$this->visited = $newArr;
	}

	public function isWall($arr, $current)
	{
		if (ord($current) - ord($arr[3]) <= 1 || ord($current) > ord($arr[3])) {
			return false;
		}

		return true;
	}

	public function isVisited($arr)
	{
		foreach ($this->visited as $key => $value) {
			if ($value[0] == $arr[0] && $value[1] == $arr[1]) {
				return true;
			}
		}

		return false;
	}

	public function getNeighbours($arr)
	{
		$newArr = [];

		if ($arr[0] > 0) {
			$newArr[] = [$arr[0] - 1, $arr[1], $this->counter, $this->lines[$arr[0] - 1][$arr[1]]];
		}

		if ($arr[1] > 0) {
			$newArr[] = [$arr[0], $arr[1] - 1, $this->counter, $this->lines[$arr[0]][$arr[1] - 1]];
		}

		if ($arr[0] < count($this->lines) - 1) {
			$newArr[] = [$arr[0] + 1, $arr[1], $this->counter, $this->lines[$arr[0] + 1][$arr[1]]];
		}

		if ($arr[1] < count($this->lines[0]) - 1) {
			$newArr[] = [$arr[0], $arr[1] + 1, $this->counter, $this->lines[$arr[0]][$arr[1] + 1]];
		}

		return $newArr;
	}


	public function findLocation($string)
	{
		foreach ($this->lines as $rowIndex => $line) {
			foreach ($line as $colIndex => $value) {
				if ($value == $string) {
					return [$rowIndex, $colIndex];
				}
			}
		}
	}

	public function printMaze($arr)
	{
		foreach ($arr as $line) {
			foreach ($line as $value) {
				print_r($value);
			}
			print_r(PHP_EOL);
		}
	}
}

