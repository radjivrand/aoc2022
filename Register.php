<?php
namespace aoc2022;

Class Register {
	public $filePath = '/Users/arne/dev/aoc2022/input_10/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_10/input_test.txt';
	public $lines;
	public $cycles;

	public function __construct(string $test)
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

		foreach ($this->lines as $key => &$value) {
			$val = explode(' ', $value);
			$res = [];
			$res['op'] = $val[0];
			$res['param'] = $val[1] ?? 0;
			$value = $res;
		}

		$this->run();
	}

	public function run()
	{
		$x = 1;
		$vals = [0 => $x];
		$cycles = 0;
		foreach ($this->lines as $key => $line) {
			if ($line['op'] == 'noop') {
				$cycles++;
				$vals[$cycles] = $x;
			} else {
				$cycles++;
				$vals[$cycles] = $x;
				$cycles++;
				$vals[$cycles] = $x;
				$x += $line['param'];
			}
		}

		$res = 0;
		foreach ([20, 60, 100, 140, 180, 220] as $value) {
			$res += $value * $vals[$value];
		}

		print_r($vals);

		print_r($res);
	}
}