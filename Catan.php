<?php
namespace aoc2022;

use Exception;

Class Catan {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_19/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_19/input_test.txt';

    public $map;
    protected $test;

    public $material = [
        'ore' => 3,
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

    protected $minutes = 0;

    protected $choices = [];

    public $currentBp = 'Blueprint 1';
    public $bp;

    public function __construct(string $test)
    {
        $this->setup($test);

        $this->bp = new Bp($this->map[$this->currentBp]);
        $tree = $this->buildDecisionTree(0, 3);

        print_r($tree);
    }

    public function buildDecisionTree($currentRound, $maxRounds) {
        if ($currentRound >= $maxRounds) {
            return null;
        }

        $root = new Node('Base case');
        // $root->insertBp($this->bp);
        $opts = $this->bp->getAvailableOptions($this->material);

        if ($root->parent == null) {
            $root->material = $this->material;
            $root->robots = $this->robots;
        }

        foreach ($opts as $label) {
            // if ($label != 'wait') {
            //     foreach ($this->map[$this->currentBp][$label] as $costItem => $cost) {
            //         $material[$costItem] -= $cost;
            //     }

            //     $robots[$label]++;
            // }

            $childNode = $this->buildDecisionTree($currentRound + 1, $maxRounds);
            $root->addChild($childNode, $label);
        }

        return $root;
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

Class Node {
    public $action;
    public $children = [];
    public $parent;
    public $material;
    public $robots;
    public $bp;

    public function __construct($action)
    {
        $this->action = $action;
    }

    public function addChild($child, $action)
    {
        if ($child !== null) {
            $this->children[] = $child;
            $child->action = $action;
            $child->parent = $this;
            $material = $this->material;

            print_r($material);


            foreach ($this->robots as $robotType => $yield) {
                $material[$robotType] = $material[$robotType] + $yield;
            }

            $this->material = $material;
            
            // $child->material = $material;
            // $child->robots = $robots;
        }
    }

    public function insertBp($bp)
    {
        $this->bp = $bp;
    }
}

Class Bp {
    public $bp;

    public function __construct($bp)
    {
        $this->bp = $bp;
    }

    public function getAvailableOptions(array $material)
    {
        $res = ['wait'];
        foreach ($this->bp as $type => $needs) {
            $ok = true;

            foreach ($needs as $label => $need) {
                if ($material[$label] < $need) {
                    $ok = false;
                    continue;
                }
            }

            if ($ok) {
                $res[] = $type;
            }
        }

        return $res;
    }
}