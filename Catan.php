<?php
namespace aoc2022;

use Exception;

Class Catan {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_19/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_19/input_test.txt';

    protected $map;
    protected $test;

    protected $material = [
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

    protected $minutes = 0;

    protected $choices = [];

    public function __construct(string $test)
    {
        $this->setup($test);

        $result = [];

        foreach (range(1,10000) as $attempt) {
            $this->reset();

            foreach (range(1, 24) as $second) {
                $this->tick('Blueprint 2');
            }

            $result[$this->material['geode']] = $this->choices;
        }

        ksort($result);
        print_r($result);

        $this->buildDecisionTree(0, 5);

    }

    public function buildDecisionTree($currentRound, $maxRounds)
    {
        if ($currentRound >= $maxRounds) {
            return null;
        }

        $root = new Node ('Round ' . ($currentRound++), $this->material, $this->robots);
        $opts = $this->getAvailableOptions($this->material); // see on puudu

        foreach ($opts as $key => $value) {
            $childNode = $this->buildDecisionTree($currentRound++, $maxRounds);
            $root->addChild($childNode);
        }

        return $root;
    }

    public function reset()
    {
        $this->choices = [];
        $this->material = [
            'ore' => 0,
            'clay' => 0,
            'obsidian' => 0,
            'geode' => 0,
        ];

        $this->robots = [
            'ore' => 1,
            'clay' => 0,
            'obsidian' => 0,
            'geode' => 0,
        ];

        $this->minutes = 0;
    }

    public function tick($blueprint)
    {
        $bp = $this->map[$blueprint];
        $options = $this->getOptions($bp);

        if (!empty($options)) {
            $rnd = rand(0, count($options) - 1);
            $selected = $options[$rnd];

            if (isset($bp[$selected])) {
                foreach ($bp[$selected] as $key => $value) {
                    $this->material[$key] -= $value;
                }
            }

            $this->choices[$this->minutes] = $selected;
        }

        foreach ($this->robots as $type => $value) {
            $this->material[$type] += $value;
        }

        if (isset($selected) && $selected != 'wait') {
            $this->robots[$selected]++;
        }
        $this->minutes++;
    }

    public function getOptions($bp)
    {
        $initial = $this->material;
        $options = [];

        foreach ($bp as $type => $component) {
            $possible = true;
            foreach ($component as $key => $value) {
                if ($initial[$key] < $value) {
                    $possible = false;
                }
            }

            if ($possible) {
                $options[] = $type;
            }
        }

        if (count($options) == 1) {
            $options[] = 'wait';
        }

        return $options;
    }

    public function add($resourceType) {
        if (!isset($this->{$resourceType})) {
            throw new Exception("No such resource", 1);
        }

        $this->{$resourceType}++;
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
    public $value;
    public $children = [];
    public $material;
    public $robots;

    public function __construct($value, $material, $robots)
    {
        $this->value = $value;
        $this->material = $material;
        $this->robots = $robots;
    }

    public function addChild($child)
    {
        $this->children[] = $child;
    }
}