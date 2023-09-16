<?php
namespace aoc2022;

require_once('Rage.php');

Class Robot {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_19/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_19/input_test.txt';


    public $map;
    public $test;
    public $currentBp;
    public $maxRobots = [];
    public $highScores = [];

    public function __construct(string $test)
    {
        $this->setup($test);
        $bpMaxScores = [];


        // part 1
        // foreach ($this->map as $key => $blueprint) {
        //     $this->currentBp = $key;
        //     $this->getMaxRobots();
        //     $maxScore = 0;

        //     foreach (range(1, 400000) as $attemptNo) {
        //         $score = $this->attempt();
        //         $maxScore = $score > $maxScore ? $score : $maxScore;
        //     }

        //     $bpMaxScores[] = $maxScore;
        //     print_r('max score for bp no ' . ($this->currentBp + 1) . ' is ' . $maxScore . PHP_EOL);
        // }

        // $result = 0;
        // foreach ($bpMaxScores as $key => $value) {
        //     $result += ($key + 1) * $value;
        // }

        // print_r($result);

        // part 2
        $result = 1;
        foreach (range(0, 2) as $bpKey) {
            $this->currentBp = $bpKey;
            $this->getMaxRobots();
            $maxScore = 0;

            foreach (range(1, 4000000) as $attemptNo) {
                $score = $this->attempt();
                $maxScore = $score > $maxScore ? $score : $maxScore;
            }

            $bpMaxScores[] = $maxScore;
            print_r('max score for bp no ' . ($this->currentBp + 1) . ' is ' . $maxScore . PHP_EOL);
            $result *= $maxScore;
        }
        print_r($result);

        // 1950 too low
        // 2475?
    }

    public function attempt()
    {
        $node = new Node('Base',
            0,
            [
                'ore' => 0,
                'clay' => 0,
                'obsidian' => 0,
                'geode' => 0,
            ],
            [
                'ore' => 1,
                'clay' => 0,
                'obsidian' => 0,
                'geode' => 0,
            ],
            []
        );

        // part1 depth = 24, part 2 depth = 32
        while ($node->depth < 32) {
            $node = $this->udpateNode($node);
        }

        return $node->material['geode'];
    }

    public function udpateNode($node)
    {
        $options = $this->getOptionsForNode($node);

        $roll = rand(0, count($options) - 1);
        $choice = $options[$roll];

        $node->choices[] = $choice;

        if ('wait' !== $choice) {
            foreach ($this->map[$this->currentBp][$choice] as $resource => $amount) {
                $node->material[$resource] -= $amount;
            }

            foreach ($node->robots as $resource => $amount) {
                $node->material[$resource] += $amount;
            }

            $node->robots[$choice]++;
        } else {
            foreach ($node->robots as $resource => $amount) {
                $node->material[$resource] += $amount;
            }
        }

        $node->depth++;

        return $node;
    }

    public function getMaxRobots()
    {
        $this->maxRobots = [
            'ore' => 0,
            'clay' => 0,
            'obsidian' => 0,
            'geode' => 24,
        ];

        foreach ($this->map[$this->currentBp] as $robot => $components) {
            foreach ($components as $componentName => $amount) {
                if ($this->maxRobots[$componentName] < $amount) {
                    $this->maxRobots[$componentName] = $amount;
                }
            }
        }
    }

    public function getOptionsForNode($node)
    {
        $options = ['wait'];
        foreach ($this->map[$this->currentBp] as $robotType => $needs) {
            $ok = true;

            foreach ($needs as $resource => $amount) {
                if ($amount > $node->material[$resource]) {
                    $ok = false;
                    continue;
                }
            }

            if ($ok && $node->robots[$robotType] < $this->maxRobots[$robotType]) {
                $options[] = $robotType;
            }
        }

        if (count($options) == 5) {
            unset($options[0]);
        }

        if (in_array('geode', $options)) {
            $options = ['geode'];
        }

        return $options;
    }

    public function setup(string $test)
    {
        $this->test = $test != '';
        $fileName = $this->test ? self::TEST_FILE_PATH : self::FILE_PATH;
        $this->map = file($fileName, FILE_IGNORE_NEW_LINES);

        $res = [];
        $blueprint = [];
        $counter = 0;

        foreach ($this->map as $key => $row) {
            [$discard, $val] = explode(': ', $row);
            $res[$key] = $val;
        }

        array_walk($res, function(&$elem) {
            $elem = ltrim($elem, 'Each ');
            $elem = rtrim($elem, '.');
            $elem = explode('. Each ', $elem);

            $new = [];
            foreach ($elem as $key => $value) {
                [$label, $content] = explode(' robot costs ', $value);
                $split = explode(' and ', $content);

                foreach ($split as $key => $value) {
                    [$amount, $resource] = explode(' ' , $value);
                    $split[$resource] = $amount;
                }

                unset($split[0]);
                if (isset($split[1])) {
                    unset($split[1]);
                }

                $new[$label] = $split ;
            }

            $elem = $new;
        });

        $this->map = $res;
    }
}
