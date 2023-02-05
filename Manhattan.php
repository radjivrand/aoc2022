<?php
namespace aoc2022;

Class Manhattan {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_15/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_15/input_test.txt';

    protected $map;
    protected $limits;
    protected $list = [];
    protected $resultPoints = [];
    protected $constraints;
    protected $resMap = [];
    protected $listOfLines = [];
    protected $intersections;

    public function __construct(string $test)
    {
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

        $this->limits = $test == '' ?
        [
            'xMin' => 0,
            'yMin' => 0,
            'xMax' => 4000000,
            'yMax' => 4000000,
        ] : 
        [
            'xMin' => 0,
            'yMin' => 0,
            'xMax' => 20,
            'yMax' => 20,
        ];

        $this->parseLines();
        // $this->fillWithPoints();
        // print_r($this->findEmpty());
        // $this->drawMap();
        $this->run();
        // print_r($this->listOfLines);

    }

    /**
     * 1. leia kõikidele nurgad e getcorners – check
     * 2. leia kõikide nurkade vahele sirged - check
     * 3. leia kõik lõikumispunktid sirgete vahel, mis jäävad sobivasse piirkonda – pmst leiab intersekti
     * 3.5: tuleb intersekti osas ära piirata, kas see asub kahe nurga vahel
     * 4. leia lõikumispunktide vahelt sellised kohad, kus 4 punkti on üksteisele väga lähedal
     */

    public function run()
    {
        foreach ($this->list as $key => &$pair) {
            $pair['corners'] = $this->getCorners($pair['sensor'], $pair['beacon']);
            $pair['lines'] = $this->getLineEndpoints($pair['corners']);
        }

        // print_r($this->list);
        // print_r($this->hasIntersect($this->listOfLines['wn_13'], $this->listOfLines['sw_13']) ? 'okei' : 'ei ole intersect');
        // $res = $this->getIntersect($this->listOfLines['wn_13'], $this->listOfLines['sw_13']);

        for ($i=0; $i < count($this->list); $i++) { 
            for ($j= $i + 1; $j < count($this->list); $j++) { 
                foreach ($this->list[$i]['lines'] as $firstLine) {
                    foreach ($this->list[$j]['lines'] as $secondLine) {
                        $intersect = $this->getIntersect($firstLine, $secondLine);
                        if (!$intersect) {
                            continue;
                        }

                        if (
                            $this->isIntersectOnSegment($intersect, $firstLine)
                            && $this->isIntersectOnSegment($intersect, $secondLine)
                        ) {
                            $this->intersections[] = $intersect;
                        }
                    }
                }
            }
        }

        foreach ($this->intersections as $key => $value) {
            if (empty($this->intersections[$key])) {
                unset($this->intersections[$key]);
            }
        }

        usort($this->intersections, function($x, $y) {
            return $x[0] <=> $y[0];

        });

        print_r($this->intersections);

        foreach ($this->intersections as $key => $value) {
            if (
                isset($this->intersections[$key + 1][0])
                && ($this->intersections[$key + 1][0] + 1 == $this->intersections[$key][0])
            ) {

                print_r('expression');
                # code...
            }
            # code...
        }

        // print_r($this->intersections);
    }

    public function getAbc($r, $s)
    {
        $a = $s['y'] - $r['y'];
        $b = $r['x'] - $s['x'];
        $c = $a * $r['x'] + $b * $r['y'];
        return [$a, $b, $c];
    }

    public function isIntersectOnSegment($intersect, $segment)
    {
        $xs = [$segment[0]['x'], $segment[1]['x']];
        sort($xs);
        $ys = [$segment[0]['y'], $segment[1]['y']];
        sort($ys);

        if (
            ($xs[0] <= $intersect[0] && $intersect[0] <= $xs[1])
            && ($ys[0] <= $intersect[1] && $intersect[1] <= $ys[1])
        ) {
            return true;
        }

        return false;
    }

    public function hasIntersect($first, $second)
    {
        $firstXvalues = [$first[0]['x'], $first[1]['x']];
        $firstYvalues = [$first[0]['y'], $first[1]['y']];
        sort($firstXvalues);
        sort($firstYvalues);

        if (
            (
                ($firstXvalues[0] <= $second[0]['x'] && $second[0]['x'] <= $firstXvalues[1])
                || ($firstXvalues[0] <= $second[1]['x'] && $second[1]['x'] <= $firstXvalues[1])
            ) && (
                ($firstYvalues[0] <= $second[0]['y'] && $second[0]['y'] <= $firstYvalues[1])
                || ($firstYvalues[0] <= $second[1]['y'] && $second[1]['y'] <= $firstYvalues[1])
            )
        ) {
            return true;
        }

        return false;
    }

    public function getIntersect($firstLine, $secondLine)
    {
        list($a1, $b1, $c1) = $this->getAbc($firstLine[0], $firstLine[1]);
        list($a2, $b2, $c2) = $this->getAbc($secondLine[0], $secondLine[1]);

        $det = $a1 * $b2 - $a2 * $b1;

        if ($det == 0) {
            return false;
        } else {
            $x = ($b2 * $c1 - $b1 * $c2) / $det;
            $y = ($a1 * $c2 - $a2 * $c1) / $det;
        }

        return [$x, $y];
    }


    public function getLineEndpoints($pair)
    {
        return [
            'ne' => [$pair['n'], $pair['e']],
            'es' => [$pair['e'], $pair['s']],
            'sw' => [$pair['s'], $pair['w']],
            'wn' => [$pair['w'], $pair['n']],
        ];
    }

    public function getCorners($a, $b)
    {
        $radius = $this->getRadius($a, $b);

        return [
            'n' => ['x' => $a[0], 'y' => $a[1] - $radius],
            'e' => ['x' => $a[0] + $radius, 'y' => $a[1]],
            's' => ['x' => $a[0], 'y' => $a[1] + $radius],
            'w' => ['x' => $a[0] - $radius, 'y' => $a[1]],
        ];
    }

    public function getBiggestRadius()
    {
        $max = 0;
        $location = [];
        foreach ($this->list as $pair) {
            $radius = $this->getRadius($pair['sensor'], $pair['beacon']);
            if ($radius > $max) {
                $max = $radius;
                $s = $pair['sensor'];
                $b = $pair['beacon'];
            }
        }
        $res = [$max, $s, $b];

        return $res;
    }

    public function fillWithPoints()
    {
        foreach ($this->list as $pairIndex => $pair) {
            // print_r('pair no: ' . $pairIndex . PHP_EOL);
            foreach (range($this->limits['yMin'], $this->limits['yMax']) as $height) {
                $radius = $this->getRadius($pair['sensor'], $pair['beacon']);
                $points = $this->getPointsForIndex($pair['sensor'], $radius, $height);
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
                'sensor' => explode(', y=', $values[0]),
                'beacon' => explode(', y=', $values[1]),
            ];

            foreach ($values as $key => &$val) {
                $val = explode(', y=', $val);

                $this->map[$val[0]][$val[1]] = $key == 0 ? 'S' : 'B';

            }
        }
    }
}
