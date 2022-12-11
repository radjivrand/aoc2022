<?php
namespace aoc2022;

Class Filesystem {
	public $filePath = '/Users/arne/dev/aoc2022/input_07/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_07/input_test.txt';
	public $lines;
	public $folders;
	public $count;

	public function __construct(string $test)
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

		$this->parseToFolders();
		$fail = true;

		// do {
		// 	$this->addSize();
		// 	$this->getRefSizes();
		// 	$newCount = $this->getWithSizeCount();
		// 	if ($newCount != $this->count) {
		// 		$this->count = $newCount;
		// 	} else {
		// 		$fail = false;
		// 	}

		// } while ($fail);
		// // } while (!$this->allSizesPresent());


		// print_r($this->count);

		$this->folders['/']['a'] = $this->folders['a'];
		$this->folders['/']['a']['e'] = $this->folders['e'];
		$this->folders['/']['d'] = $this->folders['d'];
		unset($this->folders['a']);
		unset($this->folders['d']);
		unset($this->folders['e']);

		print_r($this->folders);
	}

	public function check()
	{
		foreach ($this->folders as $key => &$folder) {

			# code...
		}

	}


	public function folderHasSize($arr)
	{
		return isset($arr['size']);
	}

	public function parseToFolders()
	{
		foreach ($this->lines as $key => $value) {
			$isLs = preg_match('/\$ ls/', $value);
			$previousIsCd = isset($this->lines[$key - 1]) ? preg_match('/\$ cd/', $this->lines[$key - 1]) : false;

			if ($isLs && $previousIsCd) {
				$curKey = $key + 1;
				do {
					$folderLabel = preg_replace('/\$ cd (.+)/', '\1', $this->lines[$key-1]);  
					$splitValue = explode(' ', $this->lines[$curKey]);
					$this->folders[$folderLabel][$splitValue[1]] = $splitValue[0];
					$curKey++;
				} while (isset($this->lines[$curKey]) && !preg_match('/\$/', $this->lines[$curKey]));
			}
		}
	}

	public function hasSubdir(array $arr): bool
	{
		foreach ($arr as $value) {
			if ($value == 'dir') {
				return true;
			}
		}
		return false;
	}

	public function addSize()
	{
		foreach ($this->folders as $foldername => &$folder) {
			$sum = 0;
			$allPresent = true;

			if (isset($folder['size'])) {
				$allPresent = false;
				continue;
			}

			foreach ($folder as $fName => $files) {
				if ($files == 'dir') {
					if (!isset($this->folders[$fName]['size'])) {
						$allPresent = false;
						break;
					}

					$sum += $this->folders[$fName]['size'];

				} else {
					$sum += $files;
				}

			}

			if ($allPresent) {
				$folder['size'] = $sum;
			}
		}
	}

	public function getRefSizes()
	{
		foreach ($this->folders as $name => &$contents) {
			foreach ($contents as $label => &$fileOrDir) {
				if ($fileOrDir == 'dir') {
					if (isset($this->folders[$label]['size'])) {
						$contents['_'.$label] = $this->folders[$label]['size'];
						unset($contents[$label]);
					}
				}
			}
		}
	}

	public function allSizesPresent()
	{
		foreach ($this->folders as $folder) {
			if (!isset($folder['size'])) {
				return false;
			}
		}
		return true;
	}

	public function getWithSizeCount()
	{
		$counter = 0;
		foreach ($this->folders as $key => $value) {
			if (isset($value['size'])) {
				$counter++;
			}
		}

		return $counter;
	}
}