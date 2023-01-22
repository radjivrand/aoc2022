<?php
namespace aoc2022;

Class Hourglass {
	const FILE_PATH = '/Users/arne/dev/aoc2022/input_14/input.txt';
	const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_14/input_test.txt';

	protected $map = [];
	protected $walls = [];
	protected $curLimits = [];
	protected $finished = false;

	public function __construct(string $test)
	{
		$fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

		$this->setWalls();
		$this->setMap();
		ksort($this->map);

		$this->curLimits = $this->findBorders();

		// for part 2
		$this->addBottom();

		$counter = 0;
		while (!$this->finished) {
			$this->drop(0, 500);
			$counter++;
		}

		$this->outputMap();
		print_r($counter - 1 . PHP_EOL);

	}

	public function addBottom()
	{
		$w = $this->curLimits['depth'];
		$start = 500 - $w - 2;
		$end = 500 + $w + 2;

		foreach (range($start, $end) as $value) {
			$this->map[$w + 2][$value] = '#';
		}

		$this->curLimits = $this->findBorders();
	}

	public function drop($height, $from)
	{
		$previous = [$height, $from];
		for ($i=$height; $i < $this->curLimits['depth'] + 1; $i++) {
			if (isset($this->map[$i][$from])) {

				if (!isset($this->map[$i][$from - 1])) {
					$this->drop($i, $from - 1);
					return;
				}

				if (!isset($this->map[$i][$from + 1])) {
					$this->drop($i, $from + 1);
					return;
				}

				$this->map[$previous[0]][$from] = 'o';
				return;
			}

			$previous = [$i, $from];
		}

		$this->finished = true;
	}

	public function findBorders()
	{
		$smallestCol = $largestCol = 500;
		$largestRow = 0;
		foreach ($this->map as $rowKey => $row) {
			if ($rowKey > $largestRow) {
				$largestRow = $rowKey;
			}

			foreach ($row as $colkey => $colValue) {
				if ($colkey > $largestCol) {
					$largestCol = $colkey;
				}

				if ($colkey < $smallestCol) {
					$smallestCol = $colkey;
				}
			}
		}

		return ['from' => $smallestCol, 'to' => $largestCol, 'depth' => $largestRow];
	}

	public function outputMap()
	{
		$limits = $this->findBorders();
		for ($i=0; $i <= $limits['depth']; $i++) {
			foreach (range($limits['from'], $limits['to']) as $value) {
				if (isset($this->map[$i][$value])) {

					print_r($this->map[$i][$value]);
				} else {
					print_r('.');
				}

			}
			print_r(PHP_EOL);
		}
	}


	public function connectPoints($a, $b)
	{
		if ($a[0] == $b[0]) {
			foreach (range($a[1], $b[1]) as $value) {
				$this->map[$value][$a[0]] = '#';
			}
		} else {
			foreach (range($a[0], $b[0]) as $value) {
				$this->map[$a[1]][$value] = '#';
			}
		}
	}

	public function setWalls()
	{
		foreach ($this->lines as $key => $line) {
			$wallCorners = explode(' -> ', $line);

			foreach ($wallCorners as $corner) {
				$coordinates = explode(',', $corner);
				$this->walls[$key][] = $coordinates;
			}
		}
	}

	public function setMap()
	{
		foreach ($this->walls as $wall) {
			foreach ($wall as $key => $corner) {
				if (isset($wall[$key + 1])) {
					$this->connectPoints($corner, $wall[$key + 1]);
				}
			}

		}
	}
}
