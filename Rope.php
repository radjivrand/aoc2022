<?php
namespace aoc2022;

Class Rope {
	public $filePath = '/Users/arne/dev/aoc2022/input_09/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_09/input_test.txt';

	public $lines;
	public $curHeadPos = ['x' => 0, 'y' => 0];
	public $curTailPos = ['x' => 0, 'y' => 0];
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

		$this->run();
	}

	public function run()
	{
		foreach ($this->lines as $key => $value) {
			for ($i=0; $i < $value['len']; $i++) { 
				switch ($value['dir']) {
					case 'R':
						$this->curHeadPos['x']++;
						while (!$this->tailIsNear()) {
							if ($this->curHeadPos['y'] != $this->curTailPos['y']) {
								$this->curTailPos['y'] = $this->curHeadPos['y'];
							}
							$this->curTailPos['x']++;
						} 
						break;
					
					case 'U':
						$this->curHeadPos['y']++;
						while (!$this->tailIsNear()) {
							if ($this->curHeadPos['x'] != $this->curTailPos['x']) {
								$this->curTailPos['x'] = $this->curHeadPos['x'];
							}
							$this->curTailPos['y']++;
						} 
						break;
					
					case 'L':
						$this->curHeadPos['x']--;
						while (!$this->tailIsNear()) {
							if ($this->curHeadPos['y'] != $this->curTailPos['y']) {
								$this->curTailPos['y'] = $this->curHeadPos['y'];
							}
							$this->curTailPos['x']--;
						} 
						break;
					
					case 'D':
						$this->curHeadPos['y']--;
						while (!$this->tailIsNear()) {
							if ($this->curHeadPos['x'] != $this->curTailPos['x']) {
								$this->curTailPos['x'] = $this->curHeadPos['x'];
							}
							$this->curTailPos['y']--;
						} 
						break;
					
					default:
						break;
				}
				$this->tailTrack[] = $this->curTailPos;
			}
			$this->output();
		}

		$res = [];
		foreach ($this->tailTrack as $value) {
			$res[] = $value['x'] . '-' . $value['y'];
		}

		print_r(count(array_unique($res)));
	}

	public function tailIsNear()
	{
		$xH = $this->curHeadPos['x'];
		$yH = $this->curHeadPos['y'];
		$xT = $this->curTailPos['x'];
		$yT = $this->curTailPos['y'];
		if ($xH > $xT && ($xH - $xT > 1)) {
			return false;
		}

		if ($yH > $yT && ($yH - $yT > 1)) {
			return false;
		}

		if ($xH < $xT && ($xT - $xH > 1)) {
			return false;
		}

		if ($yT > $yH && ($yT - $yH > 1)) {
			return false;
		}

		return true;
	}

	public function output()
	{
		for ($i=6; $i >= 0; $i--) { 
			for ($j=0; $j < 6; $j++) {
				if ($j == $this->curTailPos['x'] 
					&& $i == $this->curTailPos['y'] 
					&& $j == $this->curHeadPos['x'] 
					&& $i == $this->curHeadPos['y']
				) {
					print_r('H');
				} else if ($j == $this->curHeadPos['x'] && $i == $this->curHeadPos['y']) {
					print_r('H');
				} else if ($j == $this->curTailPos['x'] && $i == $this->curTailPos['y']) {
					print_r('T');
				} else {
					print_r('.');
				}
			}
			print_r(PHP_EOL);
		}
		print_r(PHP_EOL);
	}
}
