<?php
namespace aoc2022;

use SplQueue;

Class BFS {
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
		$this->visited[] = [...$this->start, ord('`'), '`'];
		print_r([...$this->end, ord('{'), '{']);

		$res = $this->work();

		print_r($res);
	}

	public function work()
	{
		$q = new SplQueue();
		$q->enqueue([...$this->start, ord('`'), '`']);
		$currentHigh = ord('`');

		while (!$q->isEmpty()) {
			$current = $q->dequeue();

			if (!$this->inVisited($current)) {
				$this->visited[] = $current;
			}

			if ($current[2] > $currentHigh) {
				$currentHigh = $current[2];
			}

			if ($current[0] == $this->end[0] && $current[1] == $this->end[1]) {
				return $this->visited;
			}

			$neighbours = $this->getNeighbours($current);

			foreach ($neighbours as $node) {
				if ($node[2] < $current[2] || $node[2] - $current[2] > 1 || $node[2] < $currentHigh) {
					continue;
				}

				if (!$this->inVisited($node)) {
					$q->enqueue($node);
				}
			}
		}

		return [];
	}

	public function inVisited($cell)
	{
		foreach ($this->visited as $visitedCell) {
			if ($visitedCell[0] == $cell[0] && $visitedCell[1] == $cell[1]) {
				return true;
			}
		}

		return false;
	}

	public function getNeighbours($cell) // anna ainult naabrid tagasi. kontrolli, et oleks piirides
	{
		$arr = [];

		if ($cell[0] > 0) {
			$arr[] = [$cell[0] - 1, $cell[1], ord($this->lines[$cell[0] - 1][$cell[1]]), $this->lines[$cell[0] - 1][$cell[1]]];
		}

		if ($cell[0] < count($this->lines) - 1) {
			$arr[] = [$cell[0] + 1, $cell[1], ord($this->lines[$cell[0] + 1][$cell[1]]), $this->lines[$cell[0] + 1][$cell[1]]];
		}

		if ($cell[1] > 0) {
			$arr[] = [$cell[0], $cell[1] - 1, ord($this->lines[$cell[0]][$cell[1] - 1]), $this->lines[$cell[0]][$cell[1] - 1]];
		}

		if ($cell[1] < count($this->lines[0]) - 1) {
			$arr[] = [$cell[0], $cell[1] + 1, ord($this->lines[$cell[0]][$cell[1] + 1]), $this->lines[$cell[0]][$cell[1] + 1]];
		}

		return $arr;
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