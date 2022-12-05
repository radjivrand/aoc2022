<?php
namespace aoc2022;

Class Crane {
	public $filePath = '/Users/arne/dev/aoc2022/input_05/input.txt';
	public $testPath = '/Users/arne/dev/aoc2022/input_05/input_test.txt';

	public $lines;
	public $stack = [];
	public $instructions = [];
	public $counter = 0;
	public $res = '';

	public function __construct(string $test)
	{
		$fileName = $test == '' ? $this->filePath : $this->testPath;
		$this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

		$flag = false;
		foreach ($this->lines as $key => $line) {
			if (empty($line)) {
				$flag = true;
				continue;
			}

			if ($flag) {
				array_push($this->instructions, $line);
			} else {
				array_push($this->stack, $line);
			}
		}

		foreach ($this->instructions as &$instr) {
			$qty = preg_replace('/move\s(\d+)\s.+/', '$1', $instr);
			$from = preg_replace('/.+from\s(\d).+/', '$1', $instr);
			$to = preg_replace('/.+to\s(\d)/', '$1', $instr);
			$instr = [
				'qty' => (int)$qty,
				'from' => (int)$from - 1,
				'to' => (int)$to - 1,
			];
		}

		krsort($this->stack);

		$realStack = [];
		foreach ($this->stack as &$stack) {
			$stack = str_split($stack);
		}

		$indexes = array_flip(reset($this->stack));
		unset($indexes[' ']);

		foreach ($indexes as $index) {
			if ($index == 0) {
				continue;
			}
			$values = array_column($this->stack, $index);
			$realStack[] = array_filter($values, function ($value) {
				return $value != ' ';
			});
		}

		$this->stack = $realStack;

		foreach ($this->stack as &$stack) {
			array_shift($stack);
		}
	}

	public function work()
	{
		foreach ($this->instructions as $instr) {
			for ($i=0; $i < $instr['qty']; $i++) {
				if (!empty($this->stack[$instr['from']])) {
					array_push($this->stack[$instr['to']], array_pop($this->stack[$instr['from']]));
				}
			}

			$this->counter++;
		}

		foreach ($this->stack as $stack) {
			$this->res .= end($stack);
		}
	}

	public function workMore()
	{
		foreach ($this->instructions as $instr) {
			print_r($this->stack[$instr['from']]);

			$toLift = array_slice(
				$this->stack[$instr['from']],
				count($this->stack[$instr['from']]) - $instr['qty'],
				$instr['qty']
			);
			
			array_push($this->stack[$instr['to']], ...$toLift);
			array_splice($this->stack[$instr['from']], count($this->stack[$instr['from']]) - $instr['qty']);
		}

		foreach ($this->stack as $stack) {
			$this->res .= end($stack);
		}
	}

	public function output()
	{
		print_r(PHP_EOL);
		$longest = 0;
		foreach ($this->stack as $stack) {
			if (count($stack) > $longest) {
				$longest = count($stack);
			}
		}

		for ($i=$longest; $i >= 0; $i--) {
			foreach ($this->stack as $stack) {
				print_r(isset($stack[$i]) ? $stack[$i] : ' ');
			}
			print_r(PHP_EOL);
		}
	}
}