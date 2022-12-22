<?php
namespace aoc2022;

Class Snake {
	public $filePath = '/Users/arne/dev/aoc2022/input_09/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_09/input_test_2.txt';

	public $lines;
	public $curHeadPos = ['x' => 0, 'y' => 0];
	public $body = [];
	public $tailTrack = [];

	public function __construct(string $test)
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

		foreach ($this->lines as &$line) {
			$row = explode(' ', $line);			
			$line = [
				'dir' => $row[0],
				'len' => $row[1],
			];
		}

		for ($i=1; $i <= 9; $i++) {
			$this->body[$i] = ['x' => 0, 'y' => 0];
		}

		$this->run();
	}

	public function run()
	{
		// $this->output();
		foreach ($this->lines as $command) {
			for ($i=0; $i < $command['len']; $i++) { 
				$this->move($command['dir']);
				foreach ($this->body as $key => &$value) {
					if ($key == 1) {
						if ($this->isFar($this->curHeadPos, $value)) {
							$value = $this->getNewValue($this->curHeadPos, $value);
						}
					} else {
						if ($this->isFar($this->body[$key - 1], $value)) {
							$in = $value;
							$value = $this->getNewValue($this->body[$key - 1], $value);
						}
					}
				}

				$this->tailTrack[] = $this->body[9];
				// $this->output();
				// $this->outputAll();
			}
		}

		$res = [];
		foreach ($this->tailTrack as $value) {
			$res[] = $value['x'] . ':' . $value['y'];
		}

		$this->outputTail(array_unique($res));

		print_r(count(array_unique($res)));
	}

	public function isFar($first, $second)
	{
		if (
			($first['x'] > $second['x'] && ($first['x'] - $second['x'] > 1))
			|| ($first['x'] < $second['x'] && ($second['x'] - $first['x'] > 1))
			|| ($first['y'] > $second['y'] && ($first['y'] - $second['y'] > 1))
			|| ($first['y'] < $second['y'] && ($second['y'] - $first['y'] > 1))
		) {
			return true;
		}

		return false;
	}

	public function diff($a, $b)
	{
		return abs($a - $b);
	}

	public function getNewValue($first, $second)
	{
		$initialValues = [$first, $second];

		if ($this->diff($first['x'], $second['x']) > $this->diff($first['y'], $second['y'])) {
			$second['y'] = $first['y'];
			$second['x'] += $first['x'] <=> $second['x'];
			return $second;
		}

		if ($this->diff($first['x'], $second['x']) < $this->diff($first['y'], $second['y'])) {
			$second['x'] = $first['x'];
			$second['y'] += $first['y'] <=> $second['y'];
			return $second;
		}

		if ($this->diff($first['x'], $second['x']) == $this->diff($first['y'], $second['y'])) {
			$second['x'] += $first['x'] <=> $second['x'];
			$second['y'] += $first['y'] <=> $second['y'];
			return $second;
		}

		if ($first['x'] == $second['x']) {
			$second['y'] += $first['y'] <=> $second['y'];
			return $second;
		}

		if ($first['y'] == $second['y']) {
			$second['x'] += $first['x'] <=> $second['x'];
			return $second;
		}


		return $second;
	}

	public function move($direction)
	{
		switch ($direction) {
			case 'R':
				$this->curHeadPos['x']++;
				break;
			case 'L':
				$this->curHeadPos['x']--;
				break;
			case 'U':
				$this->curHeadPos['y']++;
				break;
			case 'D':
				$this->curHeadPos['y']--;
				break;
			default:
				break;
		}
	}

	public function outputTail($arr)
	{
		foreach ($arr as $key => &$value) {
			$value = explode(':', $value);
		}

		foreach (range(12, -12) as $yValue) {
			foreach (range(-12, 12) as $xValue) {
				$show = false;
				foreach ($arr as $value) {
					if ($value[0] == $xValue && $value[1] == $yValue) {
						$show = true;
					}
				}
				print_r($show ? '#' : '.');
			}
			print_r(PHP_EOL);
		}
	}

	public function output()
	{
		for ($i=10; $i >= -10; $i--) { 
			for ($j=-10; $j < 10; $j++) {
				if ($j == $this->body[1]['x'] 
					&& $i == $this->body[1]['y'] 
					&& $j == $this->curHeadPos['x'] 
					&& $i == $this->curHeadPos['y']
				) {
					print_r('H');
				} else if ($j == $this->curHeadPos['x'] && $i == $this->curHeadPos['y']) {
					print_r('H');
				} else if ($j == $this->body[1]['x'] && $i == $this->body[1]['y']) {
					print_r('T');
				} else {
					print_r('.');
				}
			}
			print_r(PHP_EOL);
		}
		print_r(PHP_EOL);
	}

	public function outputAll()
	{
		$arr = $this->body;
		array_unshift($arr, $this->curHeadPos);

		foreach (range(12, -12) as $yValue) {
			foreach (range(-12, 12) as $xValue) {
				$curValue = '.';
				foreach (range(9, 0) as $value) {
					if ($arr[$value]['x'] == $xValue && $arr[$value]['y'] == $yValue) {
						$curValue = $value;
					}
				}
				print_r($curValue);
			}
			print_r(PHP_EOL);
		}
		print_r(PHP_EOL);
	}
}