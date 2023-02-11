<?php
namespace aoc2022;

Class Yell {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_21/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_21/input_test.txt';

    protected $input;
    protected $monkeys;

    public function __construct(string $test)
    {
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);
        $this->parseInput();

        $rootRow = explode(' ', $this->monkeys['root']);

        $first = $rootRow[0];
        $second = $rootRow[2];
        
        $val = 3403989691500;
        $firstRes = $this->getSum($first);
        print_r($firstRes);
        print_r(PHP_EOL);

        $secondRes = $this->getSum($second);
        print_r($secondRes);
        print_r(PHP_EOL);

        while ($firstRes > $secondRes) {
            $this->monkeys['humn'] = $val;
            $firstRes = $this->getSum($first);
            $secondRes = $this->getSum($second);
            print_r($firstRes);
            print_r(PHP_EOL);
            print_r($secondRes);
            print_r(PHP_EOL);
            $val = $val + 1;
        }

        print_r($val - 1);

    }

    public function getSum($key)
    {
        if (is_numeric($this->monkeys[$key])) {
            return $this->monkeys[$key];
        }

        $parts = explode(' ', $this->monkeys[$key]);

        if (!is_numeric($parts[0])) {
            $first = $this->getSum($parts[0]);
        }

        if (!is_numeric($parts[2])) {
            $second = $this->getSum($parts[2]);
        }

        switch ($parts[1]) {
            case '+':
                return $first + $second;            
            case '-':
                return $first - $second;            
            case '*':
                return $first * $second;            
            case '/':
                return $first / $second;                        
            default:
                break;
        }
    }

    public function parseInput($value='')
    {
        $parsedArr = [];
        foreach ($this->input as &$row) {
            $row = explode(': ', $row);
            $parsedArr[$row[0]] = $row[1];
        }

        $this->monkeys = $parsedArr;
    }
}
