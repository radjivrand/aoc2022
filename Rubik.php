<?php
namespace aoc2022;
// part II
// 9029 too low
// 10033 too low
// 32582 too low
// 82382 wrong
// 109229 wrong

Class Rubik {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_22/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_22/input_test.txt';

    protected $input;
    protected $test;
    protected $map;
    protected $path;
    protected $squareLength;
    protected $mapSize;
    protected $faces = [];

    public function __construct(string $test)
    {
        $this->test = $test != '';
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);
        
        $this->squareLength = $test ? 4 : 50;
        $this->mapSize = $test ? 12 : 200;
        $this->parseInput($this->mapSize);

        $this->createFaces();

        $lastCursor = $this->walk();

        // results
        if ($this->test) {
            print_r(
                ($lastCursor->x + $this->squareLength + 1) * 4
                + ($lastCursor->y + $this->squareLength + 1) * 1000
                + $lastCursor->faceId + 1
            );
        } else {
            print_r(
                ($lastCursor->x + 1 + 1 * $this->squareLength) * 4
                + ($lastCursor->y + 1 + 2 * $this->squareLength) * 1000
                + $lastCursor->direction
            );            
        }
    }

    public function changeFace($pos)
    {
        $newFace = $this->test
        ? Face::$testRules[$pos['face']][$pos['dir']]
        : Face::$rules[$pos['face']][$pos['dir']]
        ;

        $newPos = match($pos['dir'] * 10 + $newFace['dir']) {
            0 => ['x' => 0, 'y' => $pos['y']],
            1 => ['x' => $this->squareLength - 1 - $pos['y'], 'y' => 0],
            2 => ['x' => $this->squareLength - 1, 'y' => $this->squareLength - 1 - $pos['y']],
            3 => ['x' => $pos['y'], 'y' => $this->squareLength - 1],
            10 => ['x' => 0, 'y' => $this->squareLength - 1 - $pos['x']],
            11 => ['x' => $pos['x'], 'y' => 0],
            12 => ['x' => $this->squareLength - 1, 'y' => $pos['x']],
            13 => ['x' => $this->squareLength - 1 - $pos['x'], 'y' => $this->squareLength - 1],
            20 => ['x' => 0, 'y' => $this->squareLength - 1 - $pos['y']],
            21 => ['x' => $pos['y'], 'y' => 0],
            22 => ['x' => $this->squareLength - 1, 'y' => $pos['y']],
            23 => ['x' => $this->squareLength - 1 - $pos['y'], 'y' => $this->squareLength - 1],
            30 => ['x' => 0, 'y' => $pos['x']],
            31 => ['x' => $this->squareLength - 1 - $pos['x'], 'y' => 0],
            32 => ['x' => $this->squareLength - 1, 'y' => $this->squareLength - 1 - $pos['x']],
            33 => ['x' => $pos['x'], 'y' => $this->squareLength - 1],
        };

        $pos = array_merge($newPos, $newFace);

        return $pos;
    }

    public function isWall($pos)
    {
        $face = $this->faces[$pos['face']];
        return $face->coords[$pos['y']][$pos['x']] == '#';
    }

    public function outOfBounds($pos)
    {
        return $pos['x'] < 0
        || $pos['y'] < 0
        || $pos['x'] > $this->squareLength - 1
        || $pos['y'] > $this->squareLength - 1;
    }

    public function walk()
    {
        $cursor = new Cursor(0, 0, 0, 0);

        foreach ($this->path as $key => $step) {
            if ($step == 'L') {
                if ($cursor->direction == 0) {
                    $cursor->direction = 3;
                } else {
                    $cursor->direction--;
                }
            }

            if ($step == 'R') {
                if ($cursor->direction == 3) {
                    $cursor->direction = 0;
                } else {
                    $cursor->direction++;
                }
            }

            if (is_numeric($step)) {
                foreach (range(1, $step) as $hop) {
                    $newPos = $cursor->getNextPos();

                    if ($this->outOfBounds($newPos)) {
                        $newPos = $this->changeFace($newPos);
                    }

                    if ($this->isWall($newPos)) {
                        continue;
                    }

                    $cursor->setPosition($newPos);
                }
            }
        }

        return $cursor;
    }

    public function createFaces()
    {
        $height = count($this->map) / $this->squareLength;
        $maxWidth = 0;
        foreach ($this->map as $row) {
            if (count($row) > $maxWidth) {
                $maxWidth = count($row);
            }
        }

        $width = $maxWidth / $this->squareLength;

        foreach (range(0, $height - 1) as $rowIndex) {
            foreach (range(0, $width - 1) as $colIndex) {
                $startingY = $rowIndex * $this->squareLength;
                $startingX = $colIndex * $this->squareLength;

                $square = [];
                for ($i=$startingY; $i < $startingY + $this->squareLength; $i++) {
                    $newRow = [];
                    for ($j=$startingX; $j < $startingX + $this->squareLength; $j++) {
                        if (!isset($this->map[$i][$j])
                            || $this->map[$i][$j] == ' '
                        ) {
                            continue 2;
                        }
                        $newRow[] = $this->map[$i][$j];
                    }
                    $square[] = $newRow;
                }

                if (!empty($square)) {
                    $this->faces[] = new Face($square);
                }
            }
        }
    }

    public function parseInput($size)
    {
        {
            foreach (range(0, $size - 1) as $row) {
                $this->map[] = str_split($this->input[$row]);
            }

            $instr = $this->input[$size + 1];

            preg_match_all('/\d+|[A-Z]/', $instr, $matches);
            $this->path = $matches[0];
        }
    }
}

Class Face {
    public $coords;
    public $rotation;
    protected $id;
    public static int $counter = 0;
    public static $testRules = [
        0 => [
            0 => ['face' => 5, 'dir' => 2],
            1 => ['face' => 3, 'dir' => 1],
            2 => ['face' => 2, 'dir' => 1],
            3 => ['face' => 1, 'dir' => 1],
        ],
        1 => [
            0 => ['face' => 2, 'dir' => 0],
            1 => ['face' => 4, 'dir' => 3],
            2 => ['face' => 5, 'dir' => 3],
            3 => ['face' => 0, 'dir' => 1],
        ],
        2 => [
            0 => ['face' => 3, 'dir' => 0],
            1 => ['face' => 4, 'dir' => 0],
            2 => ['face' => 1, 'dir' => 2],
            3 => ['face' => 0, 'dir' => 0],
        ],
        3 => [
            0 => ['face' => 5, 'dir' => 1],
            1 => ['face' => 4, 'dir' => 1],
            2 => ['face' => 2, 'dir' => 2],
            3 => ['face' => 0, 'dir' => 3],
        ],
        4 => [
            0 => ['face' => 5, 'dir' => 0],
            1 => ['face' => 1, 'dir' => 3],
            2 => ['face' => 2, 'dir' => 3],
            3 => ['face' => 3, 'dir' => 3],
        ],
        5 => [
            0 => ['face' => 0, 'dir' => 2],
            1 => ['face' => 1, 'dir' => 0],
            2 => ['face' => 4, 'dir' => 2],
            3 => ['face' => 3, 'dir' => 2],
        ],
    ];

    public static $rules = [
        0 => [
            0 => ['face' => 1, 'dir' => 0],
            1 => ['face' => 2, 'dir' => 1],
            2 => ['face' => 3, 'dir' => 0],
            3 => ['face' => 5, 'dir' => 0],
        ],
        1 => [
            0 => ['face' => 4, 'dir' => 2],
            1 => ['face' => 2, 'dir' => 2],
            2 => ['face' => 0, 'dir' => 2],
            3 => ['face' => 5, 'dir' => 3],
        ],
        2 => [
            0 => ['face' => 1, 'dir' => 3],
            1 => ['face' => 4, 'dir' => 1],
            2 => ['face' => 3, 'dir' => 1],
            3 => ['face' => 0, 'dir' => 3],
        ],
        3 => [
            0 => ['face' => 4, 'dir' => 0],
            1 => ['face' => 5, 'dir' => 1],
            2 => ['face' => 0, 'dir' => 0],
            3 => ['face' => 2, 'dir' => 0],
        ],
        4 => [
            0 => ['face' => 1, 'dir' => 2],
            1 => ['face' => 5, 'dir' => 2],
            2 => ['face' => 3, 'dir' => 2],
            3 => ['face' => 2, 'dir' => 3],
        ],
        5 => [
            0 => ['face' => 4, 'dir' => 3], //
            1 => ['face' => 1, 'dir' => 1],
            2 => ['face' => 0, 'dir' => 1],
            3 => ['face' => 3, 'dir' => 3],
        ],
    ];

    public function __construct($array)
    {
        $this->id = self::$counter++;
        $this->rotation = 0;
        $this->coords = $array;
    }

    public function output()
    {
        foreach ($this->coords as $key => $row) {
            foreach ($row as $value) {
                print_r($value);
            }
            print_r(PHP_EOL);
        }
    }
}

Class Cursor {
    public $x;
    public $y;
    public $direction;
    public $faceId;

    public function __construct($x, $y, $direction, $faceId)
    {
        $this->x = $x;
        $this->y = $y;
        $this->direction = $direction;
        $this->faceId = $faceId;
    }

    public function getNextPos()
    {
        switch ($this->direction) {
            case 0:
                $x = $this->x + 1;
                $y = $this->y;
                break;
            case 1:
                $x = $this->x;
                $y = $this->y + 1;
                break;
            case 2:
                $x = $this->x - 1;
                $y = $this->y;
                break;
            case 3:
                $x = $this->x;
                $y = $this->y - 1;
                break;
        }

        return [
            'x' => $x,
            'y' => $y,
            'dir' => $this->direction,
            'face' => $this->faceId
        ];
    }

    public function setPosition($pos)
    {
        $this->x = $pos['x'];
        $this->y = $pos['y'];
        $this->direction = $pos['dir'];
        $this->faceId = $pos['face'];

        return $this;
    }
}