<?php
namespace aoc2022;

Class Sorter {
	public $filePath = '/Users/arne/dev/aoc2022/input_07/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_07/input_test.txt';
	public $lines;
	public $totalDisk = 70000000;
	public $spaceNeeded = 30000000;

	public function __construct(string $test)
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);
		$this->parseCommands();

		$continue = true;
		while ($continue) {
			$ok = $this->findFirstFiles();
			if ($ok) {
				$this->cutAndPaste($ok);
			} else {
				$continue = false;
			}
		}

		$this->clearCommands();
		$res = $this->findSmallestFolder();
		print_r($res);
	}

	public function findSmallestFolder()
	{
		$currentSize = $this->getOuterSize();
		$currentFreeSpace = $this->totalDisk - $currentSize;
		$sizeToFind = $this->spaceNeeded - $currentFreeSpace;

		$candidates = [];
		foreach ($this->lines as $line) {
			if (preg_match_all('/(\d+)\s\:dir\s/', $line, $matches)) {
				$curSize = (int)$matches[1][0];
				if ($curSize >= $sizeToFind) {
					$candidates[] = $curSize;
				}
			}
		}

		sort($candidates);
		return $candidates[0];
	}

	public function getOuterSize()
	{
		$sum = 0;
		foreach ($this->lines as $key => $value) {
			if (preg_match_all('/^(\d+)/', $value, $matches)) {
				$curSize = (int)$matches[1][0];
				$sum += $curSize;
			}
		}

		return $sum;
	}

	public function getSumForBelowHundredK()
	{
		$sum = 0;
		foreach ($this->lines as $key => $value) {
			if (preg_match_all('/(\d+)\s\:dir\s/', $value, $matches)) {
				$curSize = (int)$matches[1][0];
				if ($curSize < 100000) {
					$sum += $curSize;
				}
			}
		}

		return $sum;
	}

	public function calculateSizes($arr)
	{
		$size = 0;
		foreach ($arr as $value) {
			$firstPart = explode(' ', $value)[0];
			if (preg_match('/^\d+/', $firstPart)) {
				$size += $firstPart;
			}
		}

		return $size;
	}

	public function clearCommands()
	{
		foreach ($this->lines as $key => $line) {
			if (is_array($line)) {
				unset($this->lines[$key]);
			}
		}
	}

	public function findFolder($folderName, $startAt)
	{
		for ($i = $startAt; $i > 0; $i--) { 
			if (isset($this->lines[$i])) {
				if (is_array($this->lines[$i])) {
					continue;
				}
				if ($this->lines[$i] == $folderName) {
					return $i;
				}
			}
		}

		return false;
	}

	public function cutAndPaste($arr)
	{
		$startIndex = array_key_first($arr);
		array_splice($this->lines, $startIndex, count($arr));

		$dirName = 'dir ' . $this->lines[$startIndex - 2]['dest'];
		$size = $this->calculateSizes($arr);

		$insertPoint = $this->findFolder($dirName, $startIndex);
		$this->lines[$insertPoint] = $size . ' :' . $this->lines[$insertPoint];

		$insertArray = array_map(function($item) {
			return '-' . $item;
		}, $arr);

		array_splice($this->lines, $insertPoint + 1, 0, $insertArray);
	}

	public function hasNoFolders($listing)
	{
		foreach ($listing as $key => $value) {
			if (explode(' ', $value)[0] == 'dir') {
				return false;
			}
		}
		return true;
	}

	public function findFirstFiles()
	{
		$segments = array_filter($this->lines, function($item) {
			return !is_array($item);
		});

		$bigArr = [];
		$tinyArr = [];
		foreach ($segments as $key => $segmentRow) {
			if (isset($segments[$key + 1])) {
				$tinyArr[$key] = $segmentRow;
			} else {
				$tinyArr[$key] = $segmentRow;
				$bigArr[] = $tinyArr;
				$tinyArr = [];
				continue;
			}
		}

		if (count($bigArr) == 1) {
			return false;
		}

		foreach ($bigArr as $arr) {
			if ($this->hasNoFolders($arr) && !empty($arr)) {
				return $arr;
			}
		}
	}

	public function parseCommands()
	{
		foreach ($this->lines as $key => &$line) {
			if ($line[0] == '$') {
				$exploded = explode(' ', $line);
				$line = ['command' => $exploded[1]];

				if (isset($exploded[2])) {
					$line['dest'] = $exploded[2];
				}
			}
		}
	}
}

