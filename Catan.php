<?php
namespace aoc2022;

use Exception;

Class Catan {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_19/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_19/input_test.txt';

    protected $map;
    protected $test;

    protected $ore = 0;
    protected $clay = 0;
    protected $obsidian = 0;
    protected $geode = 0;

    protected $robots = [
        'ore' => 1,
        'clay' => 1,
        'obsidian' => 0,
        'geode' => 0,
    ];

    protected $minutes = 0;

    public function __construct(string $test)
    {
        $this->setup($test);

        foreach (range(1, 24) as $second) {
            $this->tick('Blueprint 1');
            print_r(['robots' => $this->robots]);
            print_r([$this->ore]);
            print_r([$this->clay]);
            print_r([$this->obsidian]);
            print_r([$this->geode]);
        }
    }

    public function tick($blueprint)
    {
        $bp = $this->map[$blueprint];
        $options = $this->getOptions($bp);
        if (!empty($options)) {
            $selected = $options[0];
        }

        foreach ($this->robots as $type => $value) {
            $this->$type += $value;
        }

        if (isset($selected)) {
            $this->robots[$selected]++;
        }
        $this->minutes++;
    }

    public function getOptions($bp)
    {
        $initial = [
            'ore' => $this->ore,
            'clay' => $this->clay,
            'obsidian' => $this->obsidian,
            'geode' => $this->geode,
        ];

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

