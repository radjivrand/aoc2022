<?php
namespace aoc2022;

Class Storm {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_24/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_24/input_test.txt';

    protected $map;
    protected $test;
    protected $start;
    protected $end;
    protected $winds;
    protected $width;
    protected $height;
    protected $sampleMap;
    protected $windLog = [];
    protected $currentWind = [];
    protected $attemptNo = 0;
    protected $mapByWindTurn = [];

    //276 + 1? 277!

    public function __construct(string $test)
    {
        $this->setup($test);
        $this->getWinds();

        $startStopper = microtime(true);

        foreach (range(1, 1200) as $try) {
            $occupied = [];
            foreach ($this->winds as $key => &$wind) {
                $wind = $this->moveWind($wind);
                $occupied[] = $wind['x'] . ':' . $wind['y'];
            }

            $diff = array_diff($this->sampleMap, array_unique($occupied));

            $this->mapByWindTurn[$try] = $diff;
        }

        $firstLap = microtime(true);
        print_r('maps created: ' . round(($firstLap - $startStopper), 4)  . PHP_EOL);

        $this->run($firstLap);

        $secondLap = microtime(true);
        print_r('second lap: ' . round(($secondLap - $firstLap), 4) . PHP_EOL);
    }

    public function run($time)
    {
        $end = false;
        $start = '0_1:0';
        $visited = [$start];

        $queue = $this->getNeighbours($start);

        while (!empty($queue) && !$end) {
            $next = array_shift($queue);
            $end = preg_match('/.*_' . $this->end . '$/', $next);

            $neighbours = $this->getNeighbours($next);
            $queue = array_merge($queue, array_diff($neighbours, $queue));

            $visited[] = $next;
        }

        print_r('first part ready! ' . PHP_EOL);
        $firstStop = microtime(true);
        print_r('time: ' . round(($firstStop - $time), 4) . PHP_EOL);
        print_r('result: ' . $next);
        print_r(PHP_EOL);
        print_r(PHP_EOL);

        $visited = [$next];
        $queue = $this->getNeighbours($next);
        $end = false;
        $counter = 0;

        while (!empty($queue) && !$end) {
            $next = array_shift($queue);
            $end = preg_match('/.*_' . $this->start . '$/', $next);

            $neighbours = $this->getNeighbours($next);

            $queue = array_merge($queue, array_diff($neighbours, $queue));

            $visited[] = $next;
            $counter++;
        }

        print_r('second part ready! ' . PHP_EOL);
        $secondStop = microtime(true);
        print_r('time: ' . round(($secondStop - $firstStop), 4) . PHP_EOL);
        print_r('result: ' . $next);
        print_r(PHP_EOL);
        print_r(PHP_EOL);

        $visited = [$next];
        $queue = $this->getNeighbours($next);
        $end = false;
        $counter = 0;

        while (!empty($queue) && !$end) {
            $next = array_shift($queue);
            $end = preg_match('/.*_' . $this->end . '$/', $next);

            $neighbours = $this->getNeighbours($next);

            $queue = array_merge($queue, array_diff($neighbours, $queue));

            $visited[] = $next;
            $counter++;
        }

        print_r('third part ready! ' . PHP_EOL);
        $thirdStop = microtime(true);
        print_r('time: ' . round(($thirdStop - $secondStop), 4) . PHP_EOL);
        print_r('result: ' . $next);
        print_r(PHP_EOL);
        print_r(PHP_EOL);
    }

    public function getNeighbours(string $pos)
    {
        $initial = $pos;
        $places = $exploded = [];
        [$level, $pos] = explode('_', $pos);

        $pos = explode(':', $pos);
        $candidates = [
            ($pos[0] + 1) . ':' . ($pos[1]),
            ($pos[0]) . ':' . ($pos[1] + 1),
            ($pos[0]) . ':' . ($pos[1]),
            ($pos[0] - 1) . ':' . ($pos[1]),
            ($pos[0]) . ':' . ($pos[1] - 1),
        ];

        if (in_array($this->end, $candidates)) {
            $places[] = ($level + 1) . '_' . $this->end;
        }

        if (in_array($this->start, $candidates)) {
            $places[] = ($level + 1) . '_' . $this->start;
        }

        while (count($candidates)) {
            $elem = array_pop($candidates);
            $exploded = explode(':', $elem);

            if (
                in_array('0', $exploded)
                || in_array('-1', $exploded)
                || $exploded[0] > $this->width - 2
                || $exploded[1] > $this->height - 2
                || !in_array($elem, $this->mapByWindTurn[$level + 1])
            ) {
                continue;
            }

            $places[] = ($level + 1) . '_' . $elem;
        }

        return $places;
    }

    public function outOfBounds($wind)
    {
        return isset(
            $this->map[$wind['y']][$wind['x']]
        )
        && $this->map[$wind['y']][$wind['x']] != '.';
    }

    public function moveWind($wind)
    {
        switch ($wind['dir']) {
            case '>':
                $wind['x']++;
                if ($this->outOfBounds($wind)) {
                    $wind['x'] = $wind['x'] - $this->width + 2;
                }
                break;
            case '<':
                $wind['x']--;
                if ($this->outOfBounds($wind)) {
                    $wind['x'] = $wind['x'] + $this->width - 2;
                }
                break;
            case 'v':
                $wind['y']++;
                if ($this->outOfBounds($wind)) {
                    $wind['y'] = $wind['y'] - $this->height + 2;
                }
                break;
            case '^':
                $wind['y']--;
                if ($this->outOfBounds($wind)) {
                    $wind['y'] = $wind['y'] + $this->height - 2;
                }
                break;
        }

        // print_r($wind);
        return $wind;
    }

    public function getWinds()
    {
        $this->winds = [];
        foreach ($this->map as $rowKey => &$row) {
            foreach ($row as $colKey => &$value) {
                $wind = null;
                $wind = match($value) {
                    '>' => ['dir' => '>', 'x' => $colKey, 'y' => $rowKey],
                    '<' => ['dir' => '<', 'x' => $colKey, 'y' => $rowKey],
                    'v' => ['dir' => 'v', 'x' => $colKey, 'y' => $rowKey],
                    '^' => ['dir' => '^', 'x' => $colKey, 'y' => $rowKey],
                    '#' => null,
                    '.' => null,
                };

                if ($wind) {
                    $value = '.';
                    $this->winds[] = $wind;
                }
            }
        }
    }

    public function out()
    {
        $map = $this->map;

        foreach ($this->winds as $wind) {
            if ($map[$wind['y']][$wind['x']] == '.' ) {
                $map[$wind['y']][$wind['x']] = $wind['dir'];
            } elseif (in_array($map[$wind['y']][$wind['x']], ['>','<','v','^'])) {
                $map[$wind['y']][$wind['x']] = 2;
            } elseif (is_numeric($map[$wind['y']][$wind['x']])) {
                $map[$wind['y']][$wind['x']]++;
            }
        }

        foreach ($map as $key => $row) {
            $rowNr = $key < 10 ? '0' . (string)$key : (string)$key;

            print_r($rowNr . ' ');
            print_r(implode('', $row));
            print_r(PHP_EOL);
        }
        print_r(PHP_EOL);
    }

    public function setup($test)
    {
        $this->test = $test != '';
        $fileName = $this->test ? self::TEST_FILE_PATH : self::FILE_PATH;
        $this->map = file($fileName, FILE_IGNORE_NEW_LINES);

        foreach ($this->map as &$value) {
            $value = str_split($value);
        }

        $this->height = count($this->map);
        $this->width = count($this->map[0]);

        // $this->start = ['x' => 1, 'y' => 0];
        $this->start = '1:0';
        // $this->end = ['x' => $this->width - 2, 'y' => $this->height - 1];
        $this->end = ($this->width - 2) . ':' . ($this->height - 1);

        $this->sampleMap[] = '1:0';
        foreach (range(1, $this->height - 2) as $y) {
            foreach (range(1, $this->width - 2) as $x) {
                $this->sampleMap[] = $x . ':' . $y;
            }
        }

        $this->sampleMap[] = ($this->width - 2) . ':' . ($this->height - 2);
    }
}
