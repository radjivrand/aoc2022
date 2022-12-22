<?php
namespace aoc2022;

Class Register {
	public $filePath = '/Users/arne/dev/aoc2022/input_10/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_10/input_test.txt';
	public $lines;
	public $cycles;
	public $vals;

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
		$this->outputCrt();
	}

	public function run()
	{
		$x = 1;
		$this->vals = [0 => $x];
		$cycles = 0;
		foreach ($this->lines as $key => $line) {
			$cycles++;
			$this->vals[$cycles] = $x;

			if ($line['op'] == 'addx') {
				$cycles++;
				$this->vals[$cycles] = $x;
				$x += $line['param'];
			}

		}

		$res = 0;

		foreach ([20, 60, 100, 140, 180, 220] as $value) {
			$res += $value * $this->vals[$value];
		}
	}

	public function outputCrt()
	{
		$currentIndex = 2;
		foreach (range(0, 5) as $rowVal) {
			foreach (range(0, 40) as $colVal) {
				$spriteCenter = $this->vals[$rowVal * 40 + $colVal + 1];
				$spritePos = range($spriteCenter - 1, $spriteCenter + 1);

				$crtPos = $colVal;

				if (in_array($crtPos, $spritePos)) {
					print_r('#');
				} else {
					print_r('.');
				}
			}
			print_r(PHP_EOL);
		}
	}
}