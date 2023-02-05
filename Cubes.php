<?php
namespace aoc2022;

Class Cubes {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_18/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_18/input_test.txt';

    protected $input;
    protected $cubes;
    protected $height;

    public function __construct(string $test)
    {
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);

        $this->parseCubes();
        $this->height = count($this->input);
        $count = $this->run();
        print_r($count);
    }

    public function run()
    {
        $count = 0;
        for ($i=0; $i < $this->height; $i++) { 
            for ($j= $i + 1; $j < $this->height; $j++) { 
                if ($this->isNear($this->cubes[$i], $this->cubes[$j])) {
                    $count++;
                }
            }
        }

        return $this->height * 6 - $count * 2;
    }

    public function isNear($a, $b)
    {
        return (
            ($a['x'] == $b['x'] && $a['y'] == $b['y'] && abs($a['z'] - $b['z']) == 1)
            || ($a['x'] == $b['x'] && abs($a['y'] - $b['y']) == 1 && $a['z'] == $b['z'])
            || (abs($a['x'] - $b['x']) == 1 && $a['y'] == $b['y'] && $a['z'] == $b['z'])
        );
    }

    public function parseCubes()
    {
        foreach ($this->input as $key => &$row) {
            $exploded = explode(',', $row);
            $newRow = [
                'x' => $exploded[0],
                'y' => $exploded[1],
                'z' => $exploded[2],
            ];
            $this->cubes[] = $newRow;
        }
    }
}
