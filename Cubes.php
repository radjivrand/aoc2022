<?php
namespace aoc2022;

Class Cubes {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_18/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_18/input_test_test.txt';

    protected $input;
    protected $cubes;
    protected $height;
    protected $threeD;
    protected $minX;
    protected $minY;
    protected $minZ;
    protected $maxX;
    protected $maxY;
    protected $maxZ;

    public function __construct(string $test)
    {
        // 2646 too high
        // 2426 too low
        // 2434 too low
        // 2438 -- wrong
        // 2734 not right

        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);

        $this->findLimits();

        // part I
        // print_r($this->countSides($this->input));

        // part II
        $res = $this->walkSpace();

        $emptyRoom = [];
        foreach (range($this->minX, $this->maxX) as $x) {
            foreach (range($this->minY, $this->maxY) as $y) {
                foreach (range($this->minZ, $this->maxZ) as $z) {
                    $emptyRoom[] = $this->toString([$x,$y,$z]);
                }
            }
        }

        $diff = array_diff($emptyRoom, $res);

        print_r($this->countSides($diff));
    }

    public function countSides($arr)
    {
        $sides = 0;

        foreach ($arr as $cube) {
            list($x, $y, $z) = explode(',', $cube);

            $sides += !in_array($x - 1 . ',' . $y . ',' . $z, $arr) ? 1 : 0;
            $sides += !in_array($x + 1 . ',' . $y . ',' . $z, $arr) ? 1 : 0;
            $sides += !in_array($x . ',' . $y - 1 . ',' . $z, $arr) ? 1 : 0;
            $sides += !in_array($x . ',' . $y + 1 . ',' . $z, $arr) ? 1 : 0;
            $sides += !in_array($x . ',' . $y . ',' . $z - 1, $arr) ? 1 : 0;
            $sides += !in_array($x . ',' . $y . ',' . $z + 1, $arr) ? 1 : 0;
        }

        return $sides;
    }

    public function findLimits()
    {
        $this->maxX = $this->maxY = $this->maxZ = 0;
        $this->minX = $this->minY = $this->minZ = 10;

        foreach ($this->input as $key => $value) {
            list($x, $y, $z) = explode(',', $value);

            if ($x > $this->maxX) {
                $this->maxX = $x;
            }

            if ($x < $this->minX) {
                $this->minX = $x;
            }

            if ($y > $this->maxY) {
                $this->maxY = $y;
            }

            if ($y < $this->minY) {
                $this->minY = $y;
            }

            if ($z > $this->maxZ) {
                $this->maxZ = $z;
            }

            if ($z < $this->minZ) {
                $this->minZ = $z;
            }
        }
    }

    public function walkSpace()
    {
        $queue = [];
        $visited = [];

        $queue = ['2,2,2'];

        while (!empty($queue)) {
            $currentCube = array_shift($queue);

            foreach ($this->getNeighbours($currentCube) as $neighbour) {
                if (
                    !in_array($neighbour, $visited)
                    && !in_array($neighbour, $this->input)
                    && !in_array($neighbour, $queue)
                ) {
                    $queue[] = $neighbour;
                }
            }

            $visited[] = $currentCube;
        }

        return $visited;
    }

    public function toCube($cubeString)
    {
        return explode(',', $cubeString);
    }

    public function toString($cubeArray)
    {
        return implode(',', $cubeArray);
    }

    public function outOfBounds($cube)
    {
        return $cube[0] < $this->minX - 1
            || $cube[0] > $this->maxX + 1
            || $cube[1] < $this->minY - 1
            || $cube[1] > $this->maxY + 1
            || $cube[2] < $this->minZ - 1
            || $cube[2] > $this->maxZ + 1;
    }

    public function getNeighbours($cube)
    {
        $cube = $this->toCube($cube);
        $cubes = $res = [];

        $cubes[] = [$cube[0], $cube[1], $cube[2] - 1];
        $cubes[] = [$cube[0], $cube[1], $cube[2] + 1];
        $cubes[] = [$cube[0], $cube[1] - 1, $cube[2]];
        $cubes[] = [$cube[0], $cube[1] + 1, $cube[2]];
        $cubes[] = [$cube[0] - 1, $cube[1], $cube[2]];
        $cubes[] = [$cube[0] + 1, $cube[1], $cube[2]];

        foreach ($cubes as $cube) {
            if (!$this->outOfBounds($cube)) {
                $res[] = $this->toString($cube);
            }
        }

        return $res;
    }
}
