<?php
namespace aoc2022;

Class Valve {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_16/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_16/input_test.txt';

    protected $input;
    protected $valves = [];

    public function __construct(string $test)
    {
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);

        $this->parseInput();

        // print_r($this->valves);

        $this->showPossibilities();
    }

    public function showPossibilities()
    {
        $next = 'AA';

        $visited = ['AA'];
        $queue = [];

        $time = 0;

        while (!empty($queue) || $time == 0) {
            $time++;
            $candidates = $this->getNext($next);

            foreach ($candidates as $candidate) {
                if (!in_array($candidate, $visited) && !in_array($candidate, $queue)) {
                    $queue[] = $candidate;
                }
            }

            $next = array_shift($queue);
            $visited[] = $next;
        }
    }

    public function getNext($current)
    {
        return $this->valves[$current]['con'];
    }

    public function parseInput()
    {
        foreach ($this->input as $line) {
            $valve = [];
            list($a, $b) = explode(' has flow rate=', $line);
            // $valve['name'] = explode(' ', $a)[1];
            list($rate, $connections) = preg_split('/; tunnels* leads* to valves* /', $b);
            $valve['rate'] = $rate;
            $valve['con'] = explode(', ', $connections);

            $this->valves[explode(' ', $a)[1]] = $valve;
        }
    }
}