<?php
namespace aoc2022;

Class DFS {
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

		$this->printMaze($map);
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