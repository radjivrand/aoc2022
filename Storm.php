<?php
namespace aoc2022;

Class Storm {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_24/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_24/input_test.txt';

    protected $input;
    protected $test;

    public function __construct(string $test)
    {
        $this->test = $test != '';
        $fileName = $this->test ? self::TEST_FILE_PATH : self::FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);

    }
}
