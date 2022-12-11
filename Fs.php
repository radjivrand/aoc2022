<?php
namespace aoc2022;

Class Fs {
	public $filePath = '/Users/arne/dev/aoc2022/input_07/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_07/input_test.txt';
	public $lines;
	public $folders = ['/' => [ 'parent' => 'root']];
	public $count;
	public $curFolder;
	public $parFolder;

	public $test = ['a', 'b', 'c', 'd', 'e', 'f', 'g'];

	public function __construct(string $test)
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

		// foreach ($this->lines as $key => $line) {
		// 	// print_r('alkafjdl');
		// 	print_r($line);
		// 	print_r(':   ');
		// 	if (preg_match('/\$ ls/', $line)) {
		// 		print_r($this->hasFoldersAfterLs($key) ? 'jep' : 'ei');
		// 	}

		// 		print_r(PHP_EOL);
		// 	# code...
		// }

			$this->shuffle();


			print_r($this->lines);
		// if is dir
		// goto where dir

		// contains folders? 
		// 	- no: replace with files array
		// 	- yes: cd dir
		// 		contains folders?
		// 			- no: replace with files array
		// 			- yes: cd dir
							
	}

	public function shuffle()
	{
		foreach ($this->lines as $key => &$value) {

			if (preg_match('/\$ ls/', $value)) {
				$arr = [];

				$curKey = $key++;
				$arr = [];
				$removableIndexes = [];
				do {
					$arr[] = $this->lines[$curKey];
					$removableIndexes[] = $curKey;
					$curKey++;
				} while (isset($this->lines[$curKey]) && !preg_match('/\$/', $this->lines[$curKey]));

				if ($this->hasFoldersAfterLs($key)) {
					print_r($this->lines);
					die();
					$this->shuffle();


				} else {
					$value = $arr;
					foreach ($removableIndexes as $value) {
						unset($this->lines[$value]);
					}
				}
			}
		}

	}

	// toimib
	public function isDir($string)
	{
		if (preg_match('/dir (.+)/', $string, $matches)) {
			return $matches[1];
		}
		return false;
	}

	// toimib
	public function hasFoldersAfterLs($currentKey)
	{
		$currentKey++;
		do {
			if (isset($this->lines[$currentKey]) && $this->isDir($this->lines[$currentKey])) {
				return true;
			}

			$currentKey++;
		} while (isset($this->lines[$currentKey]) && !preg_match('/\$/', $this->lines[$currentKey]));

		return false;
	}















	public function walk()
	{
		foreach ($this->lines as $key => $line) {
			if ($key == 0) {
				$this->curFolder = ['/'];
				continue;
			}

			if (preg_match('/\$ ls/', $line, $matches)) {
				$this->ls($key);
			}

			if (preg_match('/\$ cd (.+)/', $line, $matches)) {
				$this->cd($matches[1]);
			}
		}
	}

	public function cd($command)
	{
		if ($command == '..') {
			array_pop($this->curFolder);
		} else {
			array_push($this->curFolder, $command);
		}
	}

	public function ls($keyToStart)
	{
		$curKey = $keyToStart + 1;
		$arr = [];
		do {
			$arr[] = $this->lines[$curKey];
			$curKey++;
		} while (isset($this->lines[$curKey]) && !preg_match('/\$/', $this->lines[$curKey]));

		$parFolder = $this->curFolder;
		$arr['path'] = implode(':', $this->curFolder);
		array_pop($parFolder);
		if (empty($parFolder)) {

			$arr['parent'] = implode(':', $parFolder);
		}
	
		if ($this->foldersPresent($arr)) {

		} else {
			return $arr;
		}
	}

	public function foldersPresent($arr)
	{
		foreach ($arr as $key => $value) {
			if ($key == 'path') {
				continue;
			}

			if (preg_match('/$dir /', $value)) {
				return false;
			}
		}

		return true;
	}
}

