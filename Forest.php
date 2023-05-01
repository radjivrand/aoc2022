<?php
namespace aoc2022;

// 1216 too low
// 109224 too high
// 64256 is right!

// part II
// 9029 too low
// 10033 too low

Class Forest {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_22/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_22/input_test.txt';
    const RIGHT = 0;
    const DOWN = 1;
    const LEFT = 2;
    const UP = 3;

    protected $input;
    protected $split;
    protected $map = [];
    protected $instructions;
    protected $mode = '3d';
    protected $test;

    public function __construct(string $test)
    {
        $this->test = $test != '';
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);
        $this->split = $test == '' ? 199 : 11;
        $this->parseInput();

        $cursor = new Cursor($this->findStart(), 0, 0);
        $this->walk($cursor);

        $positions = [
            ['x' => 0, 'y' => 99, 'facing' => $this::UP],
            ['x' => 49, 'y' => 99, 'facing' => $this::UP],
            ['x' => 50, 'y' => -1, 'facing' => $this::UP],
            ['x' => 99, 'y' => -1, 'facing' => $this::UP],
            ['x' => 100, 'y' => -1, 'facing' => $this::UP],
            ['x' => 149, 'y' => -1, 'facing' => $this::UP],

            ['x' => 150, 'y' => 0, 'facing' => $this::RIGHT],
            ['x' => 150, 'y' => 49, 'facing' => $this::RIGHT],
            ['x' => 100, 'y' => 50, 'facing' => $this::RIGHT],
            ['x' => 100, 'y' => 99, 'facing' => $this::RIGHT],
            ['x' => 100, 'y' => 100, 'facing' => $this::RIGHT],
            ['x' => 100, 'y' => 149, 'facing' => $this::RIGHT],
            ['x' => 50, 'y' => 150, 'facing' => $this::RIGHT],
            ['x' => 50, 'y' => 199, 'facing' => $this::RIGHT],

            ['x' => 0, 'y' => 200, 'facing' => $this::DOWN],
            ['x' => 49, 'y' => 200, 'facing' => $this::DOWN],
            ['x' => 50, 'y' => 150, 'facing' => $this::DOWN],
            ['x' => 99, 'y' => 150, 'facing' => $this::DOWN],
            ['x' => 100, 'y' => 50, 'facing' => $this::DOWN],
            ['x' => 149, 'y' => 50, 'facing' => $this::DOWN],

            ['x' => 49, 'y' => 0, 'facing' => $this::LEFT],
            ['x' => 49, 'y' => 49, 'facing' => $this::LEFT],
            ['x' => 49, 'y' => 50, 'facing' => $this::LEFT],
            ['x' => 49, 'y' => 99, 'facing' => $this::LEFT],
            ['x' => -1, 'y' => 100, 'facing' => $this::LEFT],
            ['x' => -1, 'y' => 149, 'facing' => $this::LEFT],
            ['x' => -1, 'y' => 150, 'facing' => $this::LEFT],
            ['x' => -1, 'y' => 199, 'facing' => $this::LEFT],
        ];

        // foreach ($positions as $testPos) {
        //     $outBound = $this->isOutOfBounds($testPos);
        //     // print_r($outBound ? 'out' : 'in');

        //     // print_r($testPos);


        //     $res = $this->getWrappedPos($testPos);
        //     // print_r($res);

        //     print_r('to ' . $this->saySquare($res));
        //     print_r(PHP_EOL);

        //     print_r('________________' . PHP_EOL . PHP_EOL);
        // }


        print_r($cursor);
        print_r($this->saySquare(['x' => $cursor->x, 'y' => $cursor->y, 'facing' => $cursor->facing]));
        // print_r(1000 * 9 + 4 * 6 + 5);

        // print_r(($cursor->y + 1) * 1000 + ($cursor->x + 1) * 4 + $cursor->facing);
    }

    public function saySquare($position)
    {
        $dirString = '';
        switch ($position['facing']) {
            case 0:
                $dirString = 'RIGHT';
                break;
            case 1:
                $dirString = 'DOWN';
                break;
            case 2:
                $dirString = 'LEFT';
                break;
            case 3:
                $dirString = 'UP';
                break;
        }

        if (
            in_array($position['x'], range(50, 99))
            && in_array($position['y'], range(0, 49))
        ) {
            return 'CYAN ' . $dirString;
        }

        if (
            in_array($position['x'], range(100, 149))
            && in_array($position['y'], range(0, 49))
        ) {
            return 'MAGENTA ' . $dirString;
        }

        if (
            in_array($position['x'], range(50, 99))
            && in_array($position['y'], range(50, 99))
        ) {
            return 'GREEN ' . $dirString;
        }

        if (
            in_array($position['x'], range(0, 49))
            && in_array($position['y'], range(100, 149))
        ) {
            return 'RED ' . $dirString;
        }

        if (
            in_array($position['x'], range(50, 99))
            && in_array($position['y'], range(100, 149))
        ) {
            return 'BLUE ' . $dirString;
        }

        if (
            in_array($position['x'], range(0, 49))
            && in_array($position['y'], range(150, 199))
        ) {
            return 'ORANGE ' . $dirString;
        }
    }

    public function printMap($map)
    {
        foreach ($map as $row) {
            $rowArr = str_split($row);
            foreach ($rowArr as $value) {
                print_r($value);
            }
            print_r(PHP_EOL);
        }
        print_r(PHP_EOL);
    }

    public function createColumnString($index)
    {
        $map = $this->map;
        foreach ($map as &$row) {
            $row = str_split($row);
        }

        return implode('', array_column($map, $index));
    }

    public function findWrapFromString($mapString, $direction)
    {
        $dotPos = $direction ? strpos($mapString, '.') : strrpos($mapString, '.');
        $hashPos = $direction ? strpos($mapString, '#') : strrpos($mapString, '#');

        if ($hashPos === false) {
            return $dotPos;
        }

        if ($dotPos === false) {
            return $hashPos;
        }

        if ($direction) {
            return $dotPos > $hashPos ? $hashPos : $dotPos;
        } else {
            return $dotPos < $hashPos ? $hashPos : $dotPos;
        }
    }

    public function getCubePos($position)
    {
        //cyan up
        if (
            $position['y'] < 0
            && $position['x'] > 49
            && $position['x'] < 100
            && $position['facing'] == 3
        ) {
            print_r('from: cyan up' . PHP_EOL);
            $position['y'] = $position['x'] + 100;
            $position['x'] = 0;
            $position['facing'] = 0;
            return $position;
        }

        //magenta up
        if (
            $position['y'] < 0
            && $position['x'] > 99
            && $position['facing'] == 3
        ) {
            $position['y'] = 199;
            print_r('from: magenta up' . PHP_EOL);
            $position['x'] = $position['x'] - 100;
            $position['facing'] = 3;
            return $position;
        }

        //red up
        if (
            $position['y'] < 100
            && $position['x'] < 50
            && $position['facing'] == 3
        ) {
            $position['y'] = $position['x'] + 50;
            print_r('from: red up' . PHP_EOL);
            $position['x'] = 50;
            $position['facing'] = 0;
            return $position;
        }

        //magenta right
        if (
            $position['x'] > 149
            && $position['y'] < 50
            && $position['facing'] == 0
        ) {
            $position['x'] = 99;
            print_r('from: magenta right' . PHP_EOL);
            $position['y'] = 149 - $position['y'];
            $position['facing'] = 2;
            return $position;
        }

        //green right
        if (
            $position['x'] > 99
            && $position['y'] > 49
            && $position['y'] < 100
            && $position['facing'] == 0
        ) {
            print_r('from: green right' . PHP_EOL);
            $position['x'] = $position['y'] + 50;
            $position['y'] = 49;
            $position['facing'] = 3;
            return $position;
        }

        //blue right
        if (
            $position['x'] > 99
            && $position['y'] > 99
            && $position['y'] < 150
            && $position['facing'] == 0
        ) {
            print_r('from: blue right' . PHP_EOL);
            $position['x'] = 149;
            $position['y'] = 149 - $position['y'];
            $position['facing'] = 2;
            return $position;
        }

        //orange right
        if (
            $position['x'] > 49
            && $position['y'] > 149
            && $position['facing'] == 0
        ) {
            $position['x'] = $position['y'] - 100;
            print_r('from: orange right' . PHP_EOL);
            $position['y'] = 149;
            $position['facing'] = 3;
            return $position;
        }

        //magenta down
        if (
            $position['x'] > 99
            && $position['y'] > 49
            && $position['facing'] == 1
        ) {
            $position['y'] = $position['x'] - 50;
            print_r('from: magenta down' . PHP_EOL);
            $position['x'] = 99;
            $position['facing'] = 2;
            return $position;
        }

        //blue down
        if (
            $position['x'] > 49
            && $position['x'] < 100
            && $position['y'] > 149
            && $position['facing'] == 1
        ) {
            print_r('from: blue down' . PHP_EOL);
            $position['y'] = $position['x'] + 100;
            $position['x'] = 49;
            $position['facing'] = 2;
            return $position;
        }

        //orange down
        if (
            $position['y'] > 199
            && $position['x'] < 50
            && $position['facing'] == 1
        ) {
            $position['x'] = $position['x'] + 100;
            print_r('from: orange down' . PHP_EOL);
            $position['y'] = 0;
            $position['facing'] = 1;
            return $position;
        }

        //orange left
        if (
            $position['y'] > 149
            && $position['x'] < 0
            && $position['facing'] == 2
        ) {
            $position['x'] = $position['y'] - 100;
            print_r('from: orange left' . PHP_EOL);
            $position['y'] = 0;
            $position['facing'] = 1;
            return $position;
        }

        //red left
        if (
            $position['y'] > 99
            && $position['y'] < 150
            && $position['x'] < 0
            && $position['facing'] == 2
        ) {
            print_r('from: red left' . PHP_EOL);
            $position['x'] = 50;
            $position['y'] = 149 - $position['y'];
            $position['facing'] = 0;
            return $position;
        }

        //green left
        if (
            $position['y'] > 49
            && $position['y'] < 100
            && $position['x'] < 50
            && $position['facing'] == 2
        ) {
            print_r('from: green left' . PHP_EOL);
            $position['x'] = $position['y'] - 50;
            $position['y'] = 100;
            $position['facing'] = 1;
            return $position;
        }

        //cyan left
        if (
            $position['y'] < 50
            && $position['x'] < 50
            && $position['facing'] == 2
        ) {
            $position['x'] = 0;
            print_r('from: cyan left' . PHP_EOL);
            $position['y'] = 149 - $position['y'];
            $position['facing'] = 0;
            return $position;
        }
    }

    public function getWrappedPos($position)
    {
        if ($this->mode == '3d') {
            return $this->getCubePos($position);
        }

        $newPos = $position;

        switch ($position['facing']) {
            case 0:
                $row = $this->map[$position['y']];
                $newPos['x'] = $this->findWrapFromString($row, true);
                break;
            case 1:
                $colString = $this->createColumnString($position['x']);
                $newPos['y'] = $this->findWrapFromString($colString, true);
                break;
            case 2:
                $row = $this->map[$position['y']];
                $newPos['x'] = $this->findWrapFromString($row, false);
                break;
            case 3:
                $colString = $this->createColumnString($position['x']);
                $newPos['y'] = $this->findWrapFromString($colString, false);
                break;
        }
        return $newPos;
    }

    public function isWall($position)
    {
        return $this->map[$position['y']][$position['x']] == '#';
    }

    public function isOutOfBounds($position)
    {
        return !isset($this->map[$position['y']][$position['x']])
        || $this->map[$position['y']][$position['x']] == ' '
        || $position['x'] < 0
        || $position['y'] < 0;
    }

    public function walk($cursor)
    {
        $map = $this->map;
        foreach ($this->instructions as $key => $instr) {
            if ($instr == 'R' || $instr == 'L') {
                $cursor->turn($instr);
            } else {
                foreach (range(1, $instr) as $step) {
                    $pos = $cursor->getNextPos();

                    if ($this->isOutOfBounds($pos)) {
                        $pos = $this->getWrappedPos($pos);
                    }

                    if (!$this->isWall($pos)) {
                        switch ($pos['facing']) {
                            case 0:
                                $map[$pos['y']][$pos['x']] = '>';
                                break;
                            case 1:
                                $map[$pos['y']][$pos['x']] = 'v';
                                break;
                            case 2:
                                $map[$pos['y']][$pos['x']] = '<';
                                break;
                            case 3:
                                $map[$pos['y']][$pos['x']] = 'A';
                                break;
                        }

                        $cursor->update($pos);
                    }
                }
            }
        }
        print_r($map);
    }

    public function findStart()
    {
        foreach (str_split($this->map[0]) as $key => $char) {
            if ($char != ' ') {
                return $key;
            }
        }
    }

    public function parseInput()
    {
        foreach (range(0, $this->split) as $row) {
            $this->map[] = $this->input[$row];
        }

        $instr = $this->input[$this->split + 2];

        preg_match_all('/\d+|[A-Z]/', $instr, $matches);
        $this->instructions = $matches[0];
    }
}

Class Cursor {
    public $x = 0;
    public $y = 0;
    public $facing = 0;

    public function __construct($x, $y, $facing)
    {
        $this->x = $x;
        $this->y = $y;
        $this->facing = $facing;
    }

    public function turn($direction)
    {
        $add = $direction == 'R' ? 1 : -1;
        $this->facing += $add;

        if ($this->facing == 4) {
            $this->facing = 0;
        }

        if ($this->facing == -1) {
            $this->facing = 3;
        }
    }

    public function getNextPos()
    {
        $x = $this->x;
        $y = $this->y;
        switch ($this->facing) {
            case 0:
                $x++;
                break;
            case 1:
                $y++;
                break;
            case 2:
                $x--;
                break;
            case 3:
                $y--;
                break;
        }

        return ['x' => $x, 'y' => $y, 'facing' => $this->facing];
    }

    public function update($position)
    {
        $this->x = $position['x'];
        $this->y = $position['y'];
        $this->facing = $position['facing'];

        return $this;
    }

}

