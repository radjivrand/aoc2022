<?php
namespace aoc2022;

use Exception;

// 1078, too low
// 1140, too low
// 1192, too low

Class Rage {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_19/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_19/input_test.txt';

    public $map;
    protected $test;

    public $material = [
        'ore' => 0,
        'clay' => 0,
        'obsidian' => 0,
        'geode' => 0,
    ];

    protected $robots = [
        'ore' => 1,
        'clay' => 0,
        'obsidian' => 0,
        'geode' => 0,
    ];

    public $maxRobots = [
    ];

    public $materialScores = [
    ];

    public $robotScores = [
        // 'ore' => 2,
        // 'clay' => 3,
        // 'obsidian' => 27,
        // 'geode' => 327,
    ];

    protected $levels = [
        '0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0,
        '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0,
        '12' => 0, '13' => 0, '14' => 0, '15' => 0, '16' => 0, '17' => 0,
        '18' => 0, '19' => 0, '20' => 0, '21' => 0, '22' => 0, '23' => 0, '24' => 0,
    ];

    protected $minutes;

    public $currentBp = 'Blueprint 1';

    public $highScores = [];

    public $masterCounter = 0;

    public function __construct(string $test)
    {
        $this->setup($test);

        $score = 0;
        $counter = 1;

        print_r(date('Y-m-d H:i:s') . PHP_EOL);
        foreach ($this->map as $key => $value) {
            // $key = 'Blueprint 1';
            $this->highScores = [];
            $this->currentBp = $key;

            // print_r($this->map[$this->currentBp]);
            $this->setScoresForBp();
            $this->getMaxRobots();

            print_r($key);
            print_r(PHP_EOL);

            $current = $this->returnMaxGeodes();
            print_r('Current score:' . $current);
            print_r(PHP_EOL);


            print_r('###############' . PHP_EOL);

            $score += $current * $counter;
            $counter++;
        }
        print_r(date('Y-m-d H:i:s') . PHP_EOL);
        print_r('Total score: ' . $score);
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


    public function setScoresForBp()
    {
        $this->materialScores = [];
        $recipe = $this->map[$this->currentBp];

        $this->materialScores['ore'] = $recipe['ore']['ore'];
        $this->materialScores['clay'] = $recipe['clay']['ore'];

        $this->materialScores['obsidian'] =
            $recipe['obsidian']['ore']
            + $recipe['obsidian']['clay'] * $this->materialScores['clay'];

        $this->materialScores['geode'] =
            $recipe['geode']['ore']
            + $recipe['geode']['obsidian'] * $this->materialScores['obsidian'];
    }

    public function returnMaxGeodes()
    {
        /* 19 ja 24 = 1186
         *  1: 1
         * 2: 0
         * 3: 0
         * 4: 0
         * 5: 0
         * 6: 3 
         * 7: 0 
         * 8: 3 
         * 9:  13
         * 10: 0 
         * 11: 13 
         * 12: 0 
         * 13: 4 
         * 14: 0 
         * 15: 0 
         * 16: 1 
         * 17: 1 
         * 18: 15 
         * 19: 1 
         * 20: 3 
         * 21: 2 
         * 22: 1 
         * 23: 0 
         * 24: 1 
         * 25: 5 
         * 26: 2 
         * 27: 1 
         * 28: 5 
         * 29: 0
         * 30: 1
         **/

        $this->minutes = 12;
        $masterNode = new Node('Base', 0, $this->material, $this->robots);
        $masterNode->parent = 'Base';
        $this->addChildren($masterNode);

        // $this->minutes = 12;
        // foreach ($this->highScores as $score => $node) {
        //     $this->addChildren($node);
        // }

        $this->minutes = 19;
        foreach ($this->highScores as $node) {
            $this->addChildren($node);
        }

        $this->minutes = 24;
        foreach ($this->highScores as $node) {
            $this->addChildren($node);
        }

        // $this->minutes = 20;
        // foreach ($this->highScores as $score => $node) {
        //     $this->addChildren($node);
        // }

        // $this->minutes = 21;
        // foreach ($this->highScores as $score => $node) {
        //     $this->addChildren($node);
        // }

        // $this->minutes = 24;
        // foreach ($this->highScores as $score => $node) {
        //     $this->addChildren($node);
        // }

        $results = array_keys($this->highScores);

        print_r($this->levels);
        // foreach ($this->highScores as $node) {
        //     print_r('Score: ' . $node->score . PHP_EOL);
        //     // print_r($node->material);
        //     print_r($node->parent->parent->material);
        // }

        $exit = false;
        $node = $this->highScores[$results[0]];
        $order = [];

        do {
            array_unshift($order, $node->label);
            $node = $node->parent;
            if ($node->depth <= 0) {
                $exit = true;
            }
        } while (!$exit);

        // print_r($order);

        return $this->highScores[$results[0]]->material['geode'];
    }

    public function checkForHighscore($node)
    {
        if ($node->score >= array_key_last($this->highScores)) {
            if (count($this->highScores) > 20) {
                array_pop($this->highScores);
            }
            $this->highScores[$node->score] = $node;
        }

        krsort($this->highScores);
    }

    public function getScore($node)
    {
        $score = 0;
 
        foreach ($node->material as $resource => $amount) {
            $score += $this->materialScores[$resource] * $amount;
        }

        foreach ($node->robots as $resource => $amount) {
            $score += $this->materialScores[$resource] * $amount;
        }

        return $score;
    }

    public function addChildren($node)
    {
        if ($node->depth >= $this->minutes) {
            return null;
        }

        $options = $this->getOptionsForNode($node);

        foreach ($options as $option) {
            $depth = $node->depth;
            $depth++;
            $this->levels[$depth]++;
            $child = new Node($option, $depth, $node->material, $node->robots);

            if ($option != 'wait') {
                $child->robots[$option] += 1;

                $needed = $this->map[$this->currentBp][$option];
                foreach ($needed as $resource => $amount) {
                    $child->material[$resource] -= $amount;
                }
            }

            foreach ($node->robots as $resource => $value) {
                $child->material[$resource] += $value;
            }

            $child->parent = $node;
            $child->score = $this->getScore($child);
            $this->checkForHighscore($child);

            $node->children[$option] = $child;

            $this->addChildren($child);
        }
    }

    public static function getOptionsForNode($node)
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

            if ($ok && $this->robots[$robotType] < $this->maxRobots[$robotType]) {
                $options[] = $robotType;
            }
        }

        if (count($options) == 4) {
            unset($options[0]);
        }

        if (in_array('geode', $options)) {
            $options = ['geode'];
        }

        return $options;
    }

    public function setup($test)
    {
        $this->test = $test != '';
        $fileName = $this->test ? self::TEST_FILE_PATH : self::FILE_PATH;
        $this->map = file($fileName, FILE_IGNORE_NEW_LINES);

        $res = [];
        $blueprint = [];
        $counter = 0;

        foreach ($this->map as $row) {
            [$key, $val] = explode(': ', $row);
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

Class Node
{
    public $parent;
    public $children = [];
    public $score;

    public function __construct(
        public string $label, 
        public int $depth, 
        public array $material,
        public array $robots,
        public array $choices,
        )
    {
    }
}