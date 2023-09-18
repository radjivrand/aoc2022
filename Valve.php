<?php
namespace aoc2022;
use SplQueue;
use DateTime;

// 1826 too low
// 1855 -> not right!!
// 1862? -> YEAH!

// pt 2: 2158?
// pt 2: 2312??? too low
 // 2337 too low
 // 2404 too low
 // 2404 too low


Class Valve {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_16/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_16/input_test.txt';

    protected $input;
    protected $valves = [];
    protected $flowing = [];
    protected $currentValveIndex = 0;
    protected $open = [];
    protected $testFlow = [
        'me' => [
            'CO',
            'IJ',
            'NA',
            'SE',
            'KF',
            'CS',
            'MN',
        ],
        'ele' => [
            'EU',
            'QN',
            'GJ',
            'AE',
            'UK',
            'DS',
            'XM',
        ],
    ];

    public function __construct(string $test)
    {
        print_r(date('H:i:s'));
        print_r(PHP_EOL);
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);

        $this->parseInput();

        $this->flowing = array_filter($this->valves, function($item) {
            return $item['rate'] > 0;
        });

        // part 1

        // $labels = array_keys($flowingValves);

        // $max = 0;
        // foreach (range(1,1000) as $attempt) {
        //     shuffle($labels);
        //     $test = $this->getScore($labels);

        //     $max = $test > $max ? $test : $max;
        // }

        // print_r($max . PHP_EOL);

        // part 2

        $currentTime = new DateTime(date('H:i:s'));
        $endTime = new DateTime('18:00:00');

        $max = 0;
        $counter = 0;
        $bestPaths = [];

        // while ($endTime > $currentTime) {
        //     $currentTime = new DateTime(date('H:i:s'));
        foreach (range(1, 1) as $attempt) {
            $this->flowing = array_filter($this->valves, function($item) {
                return $item['rate'] > 0;
            });

            shuffle($this->flowing);
            $test = $this->testElephant();

            if ($test['flow'] > $max) {
                $max = $test['flow'];

                $bestPaths = [
                    'me' => $test['my_path'],
                    'ele' => $test['elephant_path'],
                ];
            }

            $max = $test['flow'] > $max ? $test['flow'] : $max;
            $counter++;
        }

        print_r($counter . ' attempts' . PHP_EOL);
        print_r($max . PHP_EOL);
        print_r($bestPaths);
        print_r(date('H:i:s'));
        print_r(PHP_EOL);
    }

    public function testElephant()
    {
        $flow = 0;
        $this->open = [];

        $elephant = [
            'current' => 'AA',
            'next' => $this->getNextValve('ele'),
            'path' => [],
        ];

        $elephant['will_end_at'] = count(
            $this->bfs(
                $this->valves,
                $elephant['current'],
                $elephant['next']
            )
        );

        $me = [
            'current' => 'AA',
            'next' => $this->getNextValve('me'),
            'path' => [],
        ];

        $me['will_end_at'] = count(
            $this->bfs(
                $this->valves,
                $me['current'],
                $me['next']
            )
        );

        foreach (range(1, 25) as $minute) {
            $flow += $this->countCurrentFlow();

            if ($me['will_end_at'] == $minute) {
                $me['current'] = $me['next'];
                $me['path'][] = $me['current'];
                $this->open[] = $me['next'];

                if ($next = $this->getNextValve('me')) {
                    $me['next'] = $next;

                    $me['will_end_at'] = 1 + $minute + count(
                        $this->bfs(
                            $this->valves,
                            $me['current'],
                            $me['next']
                        )
                    );
                } else {
                    $me['will_end_at'] = INF;
                }
            }

            if ($elephant['will_end_at'] == $minute) {
                $elephant['current'] = $elephant['next'];
                $elephant['path'][] = $elephant['current'];
                $this->open[] = $elephant['next'];

                if ($next = $this->getNextValve('ele')) {
                    $elephant['next'] = $next;

                    $elephant['will_end_at'] = 1 + $minute + count(
                        $this->bfs(
                            $this->valves,
                            $elephant['current'],
                            $elephant['next']
                        )
                    );
                } else {
                    $elephant['will_end_at'] = INF;
                }
            }
        }

        return [
            'flow' => $flow,
            'my_path' => $me['path'],
            'elephant_path' => $elephant['path'],
        ];
    }

    public function countCurrentFlow()
    {
        $sum = 0;

        foreach ($this->open as $valve) {
            $sum += $this->valves[$valve]['rate'];
        }

        return $sum;
    }

    public function getNextValve($person = null)
    {
        // actual
        // if (empty($this->flowing)) {
        //     return null;
        // }

        // $valve = array_shift($this->flowing);
        // return $valve['label'];

        // handmade input
        if (empty($this->testFlow[$person])) {
            return null;
        }

        $res = array_shift($this->testFlow[$person]);
        return $res;
    }

    public function getScore($tubes)
    {
        $current = 'AA';
        $time = 1;
        $previousTime = 0;
        $flowRate = 0;
        $previousFlowRate = 0;
        $flown = 0;

        foreach ($tubes as $label) {
            $pathLength = count($this->bfs($this->valves, $current, $label));

            if ($pathLength + $time > 30) {
                break;
            }

            $time += $pathLength;
            $time++;

            $diff = $time - $previousTime;
            $flowRate += $this->valves[$label]['rate'];
            $current = $label;

            $flown += $diff * $previousFlowRate;

            $previousFlowRate = $flowRate;
            $previousTime = $time;
        }

        return $flown + $flowRate * (31 - $time);
    }

    public function bfs($graph, $startVert, $end)
    {
        $visited = [];
        foreach ($graph as $key => $value) {
            $visited[$key] = false;
        }

        $queue = new SplQueue();
        $queue->enqueue($startVert);
        $parents = [];

        while (!$queue->isEmpty()) {
            $v = $queue->dequeue();
            if ($v == $end) {
                return $this->returnPath($parents, $startVert, $end);
            }

            foreach ($graph[$v]['con'] as $key => $value) {
                if (!$visited[$value]) {
                    $visited[$value] = true;
                    $parents[$value] = $v;
                    $queue->enqueue($value);
                }
            }
        }
    }

    public function returnPath($arr, $start, $end)
    {
        $parent = null;
        $res = [];

        do {
            $parent = $arr[$end];
            $res[] = $parent;
            $end = $parent;
        } while ($parent != $start);

        return array_reverse($res);
    }

    public function parseInput()
    {
        foreach ($this->input as $line) {
            $valve = [];
            list($a, $b) = explode(' has flow rate=', $line);
            // $valve['name'] = explode(' ', $a)[1];
            list($rate, $connections) = preg_split('/; tunnels* leads* to valves* /', $b);
            $valve['label'] = explode(' ', $a)[1];
            $valve['rate'] = $rate;
            $valve['con'] = explode(', ', $connections);

            $this->valves[explode(' ', $a)[1]] = $valve;
        }
    }
}
