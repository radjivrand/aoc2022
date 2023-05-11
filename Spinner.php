<?php
namespace aoc2022;

Class Spinner {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_20/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_20/input_test.txt';

    // -9426 is wrong
    // 4034 too low
    // 11037!

    protected $input;
    protected $parsedInput;
    protected $test;
    protected $unvisited = [];
    protected $sorted = [];
    protected $currentElement = [];
    protected $visitingList = [];

    private $decr = 811589153;

    public function __construct(string $test)
    {
        $this->test = $test != '';
        $fileName = $this->test ? self::TEST_FILE_PATH : self::FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);
        
        $this->parseInput();
        $this->sorted = $this->unvisited = $this->visitingList = $this->parsedInput;

        for ($x = 0; $x < 10; $x++) { 
            $this->unvisited = $this->visitingList;
            $this->mix();
            // print_r($this->sorted);
        }

        print_r($this->getScore());
    }

    public function mix()
    {
        do {
            $this->currentElement = $this->findFirst();

            if ($this->currentElement['value'] > 0) {
                for ($i = 0; $i < ($this->currentElement['value'] % (count($this->sorted) - 1)); $i++) {
                    $this->move('right');
                }
            } elseif ($this->currentElement['value'] < 0) {
                for ($j = ($this->currentElement['value']  % (count($this->sorted) - 1)); $j < 0; $j++) { 
                    $this->move('left');
                }
            } else {
                continue;
            }

        } while (count($this->unvisited) > 0);
    }

    public function findFirst()
    {
        $element = array_shift($this->unvisited);
        $key = array_search($element, $this->sorted);
        $value = explode(':', $element)[0];

        return ['key' => $key, 'value' => $value, 'unique' => $element];
    }

    public function move($dir)
    {
        $key = $this->currentElement['key'];

        if ($dir == 'right') {
            $newKey = ($key + 1 == count($this->sorted)) ? 0 : $key + 1;
        } else {
            if ($key - 1 == -1) {
                $count = array_push($this->sorted, array_shift($this->sorted));
                $this->currentElement['key'] = $count - 1;
                $this->move('left');
                return;
            }

            $newKey = $key - 1;
        }

        $val = $this->currentElement['unique'];

        $switchedVal = $this->sorted[$newKey];
        $this->sorted[$key] = $switchedVal;
        $this->sorted[$newKey] = $val;

        $this->currentElement['key'] = $newKey;
    }

    public function getValueAt($index)
    {
        if ($index < count($this->sorted)) {
            $val = $this->sorted[$index];
        } else {
            $val = $this->sorted[$index % count($this->sorted)];
        }

        return explode(':', $val)[0];
    }

    public function getScore()
    {
        $initialIndex = array_search('0', $this->input) ;
        $zeroIndex = array_search('0:' . $initialIndex, $this->sorted);

        return $this->getValueAt($zeroIndex + 1000)
        + $this->getValueAt($zeroIndex + 2000)
        + $this->getValueAt($zeroIndex + 3000);
    }

    public function parseInput()
    {
        foreach ($this->input as $key => $value) {
            $this->parsedInput[] = $value * $this->decr . ':' . $key;
        }
    }
}
