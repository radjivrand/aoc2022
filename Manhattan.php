<?php
namespace aoc2022;

Class Manhattan {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_15/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_15/input_test.txt';

    protected $map;
    protected $limits;
    protected $list = [];
    protected $lineValues = [];
    protected $resultPoints = [];
    protected $constraints;
    protected $resMap = [];

    public function __construct(string $test)
    {
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

        $this->limits = $test == '' ?
        [
            'xMin' => 0,
            'yMin' => 0,
            'xMax' => 40,
            'yMax' => 40,
        ] : 
        [
            'xMin' => 0,
            'yMin' => 0,
            'xMax' => 20,
            'yMax' => 20,
        ];

        $this->parseLines();

        // $this->drawMap();
        $this->fillWithPoints();
        print_r($this->findEmpty());
    }

    public function fillWithPoints()
    {
        foreach ($this->list as $pairIndex => $pair) {
            // print_r('pair no: ' . $pairIndex . PHP_EOL);
            foreach (range($this->limits['yMin'], $this->limits['yMax']) as $height) {
                $radius = $this->getRadius($pair['s'], $pair['b']);
                $points = $this->getPointsForIndex($pair['s'], $radius, $height);
                if (!empty($points)) {
                    $cropped = array_filter($points, function($elem) {
                        return $elem >= 0 && $elem <= $this->limits['xMax'];
                    });

                    foreach ($cropped as $point) {
                        if (!isset($this->map[$height][$point])) {
                            $this->map[$height][$point] = '#';
                        }
                    }
                }
            }
        }

        foreach ($this->map as &$row) {
            ksort($row);            
        }

        ksort($this->map);
    }

    public function findEmpty()
    {
        foreach (range($this->limits['xMin'], $this->limits['xMax']) as $xKey) {
            foreach (range($this->limits['yMin'], $this->limits['yMax']) as $yKey) {
                if (!isset($this->map[$yKey][$xKey])) {
                    print_r([$xKey, $yKey]);
                }
            }
        }
    }

    public function getPointsForIndex($point, $radius, $index)
    {
        $height = $point[1] - $index;

        if (abs($height) > $radius) {
            return;
        }

        $width = $radius - abs($height);
        return range($point[0] - $width, $point[0] + $width);
    }

    public function getRadius($a, $b)
    {
        return abs($a[0] - $b[0]) + abs($a[1] - $b[1]);
    }

    public function drawMap()
    {
        ksort($this->map);

        foreach (range($this->limits['yMin'], $this->limits['yMax']) as $yVal) {
            foreach (range($this->limits['xMin'], $this->limits['xMax']) as $xVal) {
                print_r($this->map[$xVal][$yVal] ?? '.');
            }
            print_r(PHP_EOL);
        }
    }

    public function parseLines()
    {
        foreach ($this->lines as $line) {
            $line = str_replace('Sensor at x=', '', $line);
            $values = explode(': closest beacon is at x=', $line);

            $this->list[] = [
                's' => explode(', y=', $values[0]),
                'b' => explode(', y=', $values[1]),
            ];

            foreach ($values as $key => &$val) {
                $val = explode(', y=', $val);

                $this->map[$val[0]][$val[1]] = $key == 0 ? 'S' : 'B';

            }
        }
    }
}
