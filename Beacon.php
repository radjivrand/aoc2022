<?php
namespace aoc2022;

Class Beacon {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_15/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_15/input_test.txt';

    protected $map;
    protected $limits;
    protected $list = [];
    protected $lineValues = [];
    protected $row;
    protected $curCounter = 0;

    public function __construct(string $test)
    {
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

        $testRowIndex = 10;
        $rowIndex = 2000000;

        $this->row = $rowIndex;

        $this->parseLines();
        // $this->findBorders();
        // $this->getOuterCorners();

        // foreach ($this->list as $pair) {
        //     $this->drawSensor($pair['s'], $pair['b']);
        //     $this->drawMap();
        //     print_r(PHP_EOL);
        //     // die();
        // }


        // print_r('radius: ' . $radius);
        // print_r(PHP_EOL);

        // $this->getPointsForIndex($sensor, $radius, $testRowIndex);

        foreach ($this->list as $pair) {

            // print_r($pair);
            $radius = $this->getRadius($pair['s'], $pair['b']);
            $this->getPointsForIndex($pair['s'], $radius, $this->row);
            $this->curCounter++;
        }

        // print_r($this->lineValues);

        $min = 0;
        $max = 0;
        foreach ($this->lineValues as $value) {
            if ($value[0] < $min) {
                $min = $value[0];
            }
            if ($value[1] > $max) {
                $max = $value[1];
            }
        }

        print_r($max - $min);
        print_r(PHP_EOL);
    }

    public function getPointsForIndex($point, $radius, $index)
    {
        $x = $point[0];
        // print_r('x: ' . $x);
        // print_r(PHP_EOL);
        $height = $point[1] - $index;
        // print_r('height: ' . $height);
        // print_r(PHP_EOL);

        if (abs($height) > $radius) {
            return;
        }

        $width = $radius - abs($height);
        // print_r('width: ' . $width);
        // print_r(PHP_EOL);

        $first = $x - $width;
        $second = $x + $width;

        $this->lineValues[$this->curCounter][0] = $first;
        $this->lineValues[$this->curCounter][1] = $second;
    }

    public function getRadius($a, $b)
    {
        return abs($a[0] - $b[0]) + abs($a[1] - $b[1]);
    }

    public function getOuterCorners()
    {
        foreach ($this->list as &$pair) {
            $beaconFound = false;
            $counter = 1;

            while (!$beaconFound) {
                $corners = $this->getCornerValues($pair['s'], $counter);

                if (
                    $this->isBeaconOnLine($corners[0], $corners[1], $pair['b'])
                    || $this->isBeaconOnLine($corners[1], $corners[2], $pair['b'])
                    || $this->isBeaconOnLine($corners[2], $corners[3], $pair['b'])
                    || $this->isBeaconOnLine($corners[3], $corners[0], $pair['b'])
                ) {
                    // $pair['corners'] = $corners;
                    $beaconFound = true;
                }

                $counter++;
            }
        }
    }

    public function distanceBetweenPoints($a, $b)
    {
        return sqrt(pow($b[1] - $a[1], 2) + pow($b[0] - $a[0], 2));
    }

    public function isBeaconOnLine($a, $b, $beacon)
    {
        return round($this->distanceBetweenPoints($a, $b), 4) ==
            round($this->distanceBetweenPoints($a, $beacon)
                + $this->distanceBetweenPoints($b, $beacon), 4);
    }

    public function getIntersectOnLine($a, $b, $line)
    {
        /**
         * teise joone võrrand on
         * punkt a kuni punkt b
         * m = y1 - y0 / x1 - x0 
         * y - y1 = m (x - x1)
         *
         */

        $res = ($line - $b[1]) / (($b[1] - $a[1]) / ($b[0] - $a[0])) + $b[0];

        if ($a[0] < $b[0]) {
            return $a[0] <= $res && $res <= $b[0];
        }

        if ($a[0] > $b[0]) {
            return $a[0] >= $res && $res >= $b[0];
        }

        if ($a[0] == $b[0]) {
            return $a[0] == $res;
        }
    }

    public function analyzeRow($rowIndex)
    {
        $col = array_column($this->map, $rowIndex);
        $counter = 0;
        foreach ($col as $value) {
            $counter += $value == '#' ? 1 : 0;
        }

        return $counter;
    }

    public function drawSensor($sensor, $beacon)
    {
        $counter = 1;
        $beaconFound = false;

        while (!$beaconFound) {
            $corners = $this->getCornerValues($sensor, $counter);
            $diags = [];
            
            $diags = array_merge(
                $this->getDiagValues($corners[0], $corners[1]),
                $this->getDiagValues($corners[1], $corners[2]),
                $this->getDiagValues($corners[2], $corners[3]),
                $this->getDiagValues($corners[3], $corners[0]),
            );

            foreach ($diags as $diagonal) {
                if (($diagonal[0] == $beacon[0] && $diagonal[1] == $beacon[1]) || $beaconFound) {
                    $beaconFound = true;
                    // continue;
                }

                if (isset($this->map[$diagonal[0]][$diagonal[1]])) {
                    continue;
                }

                $this->map[$diagonal[0]][$diagonal[1]] = '#';
            }
            $counter++;
        }
    }

    public function getDiagValues($locA, $locB)
    {
        $res = [];
        foreach (range($locA[0], $locB[0]) as $key => $value) {
            $res[$key] = [$value];
        }

        foreach (range($locA[1], $locB[1]) as $key => $value) {
            $res[$key][] = $value;
        }

        return $res;
    }

    public function getCornerValues($location, $distance)
    {
        $x = $location[0];
        $y = $location[1];

        return [
            [$x, $y + $distance],
            [$x + $distance, $y],
            [$x, $y - $distance],
            [$x - $distance, $y],
        ];
    }

    public function findBorders()
    {
        ksort($this->map);

        $yMin = 0;
        $yMax = 0;

        foreach ($this->map as $key => $value) {

            $localMin = min(array_keys($value));
            $localMax = max(array_keys($value));

            if ($localMin < $yMin) {
                $yMin = $localMin;
            }

            if ($localMax > $yMax) {
                $yMax = $localMax;
            }
        }

        $this->limits = [
            'xMin' => array_key_first($this->map),
            'xMax' => array_key_last($this->map),
            'yMin' => $yMin,
            'yMax' => $yMax,
        ];
    }

    public function drawMap()
    {
        ksort($this->map);

        foreach (range($this->limits['yMin'], $this->limits['yMax']) as $yVal) {
            foreach (range($this->limits['xMin'], $this->limits['xMax']) as $xVal) {
                if ($yVal == $this->row) {
                    print_r($this->map[$xVal][$yVal] ?? '-');
                } else {
                    print_r($this->map[$xVal][$yVal] ?? '.');
                }
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
