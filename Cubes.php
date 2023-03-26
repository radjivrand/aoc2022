<?php
namespace aoc2022;

Class Cubes {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_18/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_18/input_test.txt';

    protected $input;
    protected $cubes;
    protected $height;
    protected $threeD;

    public function __construct(string $test)
    {
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);

        $this->parseCubes();
        $this->height = count($this->input);
        // print_r($count);
        // print_r($this->cubes);
        $this->getThreeDee();
        $this->patchSingleHoles();
        // $this->drawCubes();
        $count = $this->run();

        print_r($this->threeD);
    }

    public function patchSingleHoles()
    {
        foreach (range(1, 22) as $zkey => $zvalue) {
            foreach (range(1, 22) as $ykey => $yvalue) {
                foreach (range(1, 22) as $xkey => $xvalue) {
                    if (
                        isset($this->threeD[$zvalue - 1][$yvalue][$xvalue])
                        && isset($this->threeD[$zvalue + 1][$yvalue][$xvalue])
                        && isset($this->threeD[$zvalue][$yvalue - 1][$xvalue])
                        && isset($this->threeD[$zvalue][$yvalue + 1][$xvalue])
                        && isset($this->threeD[$zvalue][$yvalue][$xvalue - 1])
                        && isset($this->threeD[$zvalue][$yvalue][$xvalue + 1])
                    ) {
                       $this->threeD[$zvalue][$yvalue][$xvalue] = true; 
                    }
                }
            }
        }
    }

    public function getThreeDee($value='')
    {
        $arr = [];
        foreach ($this->cubes as $value) {
            $arr[$value['z']][] = ['x' => $value['x'], 'y' => $value['y']];

        }

        ksort($arr);

        $newArr = [];
        foreach ($arr as $levelkey => $level) {
            $tiny = [];
            foreach ($level as $pair) {
                $tiny[$pair['y']][$pair['x']] = true;
            }
            ksort($tiny);
            $newArr[$levelkey] = $tiny;
        }

        $this->threeD = $newArr;
    }
    public function drawCubes()
    {
        foreach (range(0,22) as $zkey => $zvalue) {
            foreach (range(0,22) as $ykey => $yvalue) {
                foreach (range(0,22) as $xkey => $xvalue) {
                    print_r(isset($this->threeD[$zvalue][$yvalue][$xvalue]) ? '#' : '.');
                }
                print_r(PHP_EOL);
            }
            print_r(PHP_EOL);
        }
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
