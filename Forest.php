<?php
namespace aoc2022;

// 1216 too low
// 109224 too high
// 64256 is right!

Class Forest {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_22/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_22/input_test.txt';

    protected $input;
    protected $split;
    protected $map = [];
    protected $instructions;

    public function __construct(string $test)
    {
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);
        $this->split = $test == '' ? 199 : 11;
        $this->parseInput();

        $cursor = new Cursor($this->findStart(), 0, 0);
        $this->walk($cursor);

        print_r(($cursor->y + 1) * 1000 + ($cursor->x + 1) * 4 + $cursor->facing);
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

    public function getWrappedPos($position)
    {
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

