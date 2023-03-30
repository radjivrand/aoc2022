<?php
namespace aoc2022;

Class BeaconFinder {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_15/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_15/input_test.txt';
    const TEST_ROW = 10;
    const ROW = 2000000;

    protected $lines;
    protected $map;
    protected $row;

    public function __construct(string $test)
    {
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->row = $test == '' ? self::ROW : self::TEST_ROW;
        $this->lines = file($fileName, FILE_IGNORE_NEW_LINES);

        $this->mapSensors();

        // part I
        // $res = $this->findOccupiedAt($this->row);
        // $devices = $this->countDevices($this->map);
        // $res = $this->unionAndIntersect($res);
        // print_r($res[0]['right'] - $res[0]['left']);

        // part II
        array_walk($this->map, function(&$item) {
            unset($item['beacon']);
        });

        $specials = $this->findSensors();

        array_walk($specials, function (&$item) {
            $item['points'] = $this->getPoints($item);
        });

        foreach ($specials as $key => &$val) {
            $val['ne'] = $this->createLineFromPoints(
            $val['points']['n'], $val['points']['e']);
            $val['es'] = $this->createLineFromPoints(
            $val['points']['e'], $val['points']['s']);
            $val['sw'] = $this->createLineFromPoints(
            $val['points']['s'], $val['points']['w']);
            $val['wn'] = $this->createLineFromPoints(
            $val['points']['w'], $val['points']['n']);
        }

        $combinations = [
            [0, 1],
            [1, 2],
            [2, 3],
            [3, 0],
        ];

        foreach ($combinations as $comb) {
            print_r('combination: ' . PHP_EOL);
            $this->checkAllDirections($specials[$comb[0]], $specials[$comb[1]]);
        }

        $point = ['x' => 3270298, 'y' => 2638237];
        
        foreach ($this->map as $key => $sensor) {
            $res = $this->isPointInSquare($point, $sensor);
            if ($res) {
                print_r($key . ' < true' . PHP_EOL);
            }
        }

        $res = $point['x'] * 4000000 + $point['y'];
        print_r($res);
    }

    public function getIntersectPoint($one, $two)
    {
        return [
            ($two['const'] - $one['const']) / ($one['slope'] - $two['slope']),
            ($one['const'] * $two['slope'] - $two['const'] * $one['slope']) / ($one['slope'] - $two['slope'])
        ];
    }

    public function createLineFromPoints($pointA, $pointB)
    {
        $slope = ($pointA['y'] - $pointB['y']) / ($pointA['x'] - $pointB['x']);


        $a = -$slope;
        $b = 1;
        $c = $slope * $pointA['x'] - $pointA['y'];

        // print_r(['slope' => $a, 'const' => $c]);
        return ['slope' => $a, 'const' => $c];
    }

    public function checkAllDirections($sensorA, $sensorB)
    {
        $arr = [
            ['ne', 'es'],
            ['es', 'sw'],
            ['sw', 'wn'],
            ['wn', 'ne'],
            ['es', 'ne'],
            ['sw', 'es'],
            ['wn', 'sw'],
            ['ne', 'wn'],
        ];



        foreach ($arr as $dir) {
            // print_r($directions);
            $intersect = $this->getIntersectPoint($sensorA[$dir[0]], $sensorB[$dir[1]]);
            print_r($intersect);
        }
    }

    public function hasIntersectInArea($first, $second, $dir)
    {
        print_r($first);
        print_r($second);
        print_r($dir);
        die();

        $x = $this->findUnionSegment(
            $first[$dir[0]]['x'],
            $first[$dir[1]]['x'],
            $second[$dir[2]]['x'],
            $second[$dir[3]]['x']
        );
        
        $y = $this->findUnionSegment(
            $first[$dir[0]]['y'],
            $first[$dir[1]]['y'],
            $second[$dir[2]]['y'],
            $second[$dir[3]]['y']
        ); 

        if (!empty($x) && !empty($y)) {
            print_r([$x, $y]);
            return [$x, $y];
        }

        return false;
    }

    public function findUnionSegment($x1, $x2, $x3, $x4)
    {
        if ($x1 > $x2) {
            $mem = $x1;
            $x1 = $x2;
            $x2 = $mem;
        }

        if ($x3 > $x4) {
            $mem = $x3;
            $x3 = $x4;
            $x4 = $mem;
        }

        if ($x2 == $x4) {
            return $x1 > $x3 ? [$x1, $x4] : [$x3, $x4];
        }

        if ($x1 == $x3) {
            return $x4 > $x2 ? [$x1, $x2] : [$x1, $x4];
        }

        if ($x1 < $x3 && $x2 > $x4) {
            return [$x3, $x4];
        }

        if ($x1 > $x3 && $x2 < $x4) {
            return [$x1, $x2];
        }

        if ($x1 < $x3 && $x2 > $x3) {
            return [$x3, $x2];
        }

        if ($x3 < $x1 && $x4 > $x1) {
            return [$x1, $x4];
        }

        return false;
    }

    public function findSensors()
    {
        // find distances between sensors
        $distances = [];
        for ($i=0; $i < count($this->map); $i++) { 
            for ($j = $i + 1; $j < count($this->map); $j++) { 
                $distances[$i . '-' . $j]['between_sensors'] =
                    abs($this->map[$i]['sensor']['x'] - $this->map[$j]['sensor']['x'])
                    + abs($this->map[$i]['sensor']['y'] - $this->map[$j]['sensor']['y']);
                $distances[$i . '-' . $j]['distances_together'] = 
                    $this->map[$i]['distance']
                    + $this->map[$j]['distance'];
            }
        }

        $new = array_filter($distances, function ($item) {
            $diff = $item['between_sensors'] - $item['distances_together'];
            return  $diff > 0 && $diff == 2;
        });

        $res = array_keys($new);
        $areaList = [];
        foreach ($res as $val) {
            $exploded = explode('-', $val);
            $areaList[] = $exploded[0];
            $areaList[] = $exploded[1];
        }

        $areaWithSensors = [];
        foreach ($areaList as $value) {
            $areaWithSensors[] = $this->map[$value];
        }

        return $areaWithSensors;
    }

    public function getPoints($sensor)
    {
        return [
            'n' => ['x' => $sensor['sensor']['x'], 'y' => $sensor['sensor']['y'] - $sensor['distance']],
            'e' => ['x' => $sensor['sensor']['x'] + $sensor['distance'], 'y' => $sensor['sensor']['y']],
            's' => ['x' => $sensor['sensor']['x'], 'y' => $sensor['sensor']['y'] + $sensor['distance']],
            'w' => ['x' => $sensor['sensor']['x'] - $sensor['distance'], 'y' => $sensor['sensor']['y']],
        ];
    }

    public function isPointInSquare($pointToCheck, $squareOnMap)
    {
        $deltaX = abs($pointToCheck['x'] - $squareOnMap['sensor']['x']);
        $deltaY = abs($pointToCheck['y'] - $squareOnMap['sensor']['y']);
        return $squareOnMap['distance'] >= ($deltaX + $deltaY);
    }

    public function unionAndIntersect($arr)
    {
        $result = [];

        foreach ($arr as $key => $value) {
            if (empty($result)) {
                $result[] = $value;
                continue;
            }

            foreach ($result as $reskey => &$resval) {
                // result is smaller
                if (
                    $resval['left'] > $value['left']
                    && $resval['right'] < $value['right']
                ) {
                    $resval['left'] = $value['left'];
                    $resval['right'] = $value['right'];
                    continue 2;
                }

                // result is bigger
                if (
                    $resval['left'] <= $value['left']
                    && $resval['right'] >= $value['right']
                ) {
                    continue 2;
                }

                // intersecting
                if (
                    $value['left'] <= $resval['left']
                    && $value['right'] <= $resval['right']
                ) {
                    $resval['left'] = $value['left'];
                    continue 2;
                }

                if (
                    $value['left'] >= $resval['left']
                    && $value['right'] >= $resval['right']
                ) {
                    $resval['right'] = $value['right'];
                    continue 2;
                }

            }
        }

        return $result;
    }

    public function countDevices()
    {
        $arr = [];
        foreach ($this->map as $key => $val) {
            if ($val['sensor']['y'] == $this->row) {
                $arr[] = $val['sensor']['x'];
            }

            if ($val['beacon']['y'] == $this->row) {
                $arr[] = $val['beacon']['x'];
            }
        }

        return array_unique($arr);
    }

    public function findOccupiedAt($row)
    {
        $result = [];
        foreach ($this->map as $key => $val) {
            $miny = $val['sensor']['y'] - $val['distance'];
            $maxy = $val['sensor']['y'] + $val['distance'];
            if ($miny <= $row && $maxy >= $row) {
                $distanceToRow = abs($row - $val['sensor']['y']);
                $spread = $val['distance'] - $distanceToRow;
                $leftBound = $val['sensor']['x'] - $spread;
                $rightBound = $val['sensor']['x'] + $spread;

                $result[] = [
                    'left' => $leftBound,
                    'right' => $rightBound,
                ];
            }
        }

        return $result;
    }

    public function mapSensors()
    {
        foreach ($this->lines as $key => $line) {
            preg_match_all('/-?\d+/', $line, $matches);
            $this->map[] = [
                'sensor' => ['x' => $matches[0][0], 'y' => $matches[0][1]],
                'beacon' => ['x' => $matches[0][2], 'y' => $matches[0][3]],
            ];
            $this->map[$key]['distance'] = $this->getDistance($this->map[$key]);
        }
    }

    public function getDistance($arr)
    {
        $width = abs($arr['sensor']['x'] - $arr['beacon']['x']);
        $height = abs($arr['sensor']['y'] - $arr['beacon']['y']);
        return $width + $height;
    }
}
