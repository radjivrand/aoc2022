<?php
namespace aoc2022;

Class Brown {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_23/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_23/input_test.txt';

    protected $input;
    protected $test;
    protected $elves = [];
    protected $ideas = [];
    protected $order = ['N', 'S', 'W', 'E'];
    protected $currentDir = null;
    protected $minX = INF;
    protected $minY = INF;
    protected $maxX = -INF;
    protected $maxY = -INF;
    protected $clearCounter = 0;
    protected $stop = false;
    protected $turnCount = 0;

    public function __construct(string $test)
    {
        $this->test = $test != '';
        $fileName = $this->test ? self::TEST_FILE_PATH : self::FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);
            
        array_walk($this->input, function(&$elem) {
            $elem = str_split($elem);
        });

        $this->mapElves();

        while ($this->stop !== true) {
            $this->turn();
        }

        print_r(['turncount:' => $this->turnCount]);
        $this->printElves();

        $area = ($this->maxX - $this->minX + 1) * ($this->maxY - $this->minY + 1);
        print_r($area - count($this->elves));
    }

    public function getScore()
    {
        $area = ($this->maxX - $this->minX) * ($this->maxY - $this->minY);
        print_r($area - count($this->elves));
    }

    public function shuffleDir()
    {
        $first = array_shift($this->order);
        array_push($this->order, $first);
        $this->order = array_values($this->order);
    }

    public function turn()
    {
        print_r('turn no ' . $this->turnCount . PHP_EOL);
        $this->clearCounter = 0;
        $this->turnCount++;
        foreach ($this->elves as $key => $elf) {
            if ($this->isClearAround($elf)) {
                $this->clearCounter++;
                $this->ideas[] = $elf;
                continue;
            }

            foreach ($this->order as $dir) {
                if ($this->isClearInDir($elf, $dir)) {
                    $this->ideas[] = $this->moveToDir($elf, $dir);
                    continue 2;
                }
            }

            $this->ideas[] = $elf;
        }

        $this->removeDuplicateIdeas();

        foreach ($this->ideas as $key => $idea) {
            $this->elves[$key] = $idea;
        }

        $this->ideas = [];
        $this->shuffleDir();
        // $this->printElves();

        if (count($this->elves) == $this->clearCounter) {
            $this->stop = true;
        }
    }

    public function printElves()
    {
        foreach ($this->elves as $elf) {
            [$y, $x] = explode(':', $elf);

            if ($x < $this->minX) {
                $this->minX = $x;
            }

            if ($y < $this->minY) {
                $this->minY = $y;
            }

            if ($x > $this->maxX) {
                $this->maxX = $x;
            }

            if ($y > $this->maxY) {
                $this->maxY = $y;
            }
        }

        foreach (range($this->minY, $this->maxY) as $row) {
            foreach (range($this->minX, $this->maxX) as $col) {
                print_r(in_array($row . ':' . $col, $this->elves) ? '#' : '.' );

            }
            print_r(PHP_EOL);
        }

        print_r(PHP_EOL);
    }

    public function removeDuplicateIdeas()
    {
        $sorted = $this->ideas;
        asort($sorted);

        $unique = array_unique($sorted);
        $diff = array_diff_assoc($sorted, $unique);

        if ($diff != []) {
            foreach ($sorted as $key => $value) {
                if (in_array($value, $diff)) {
                    unset($sorted[$key]);
                }
            }
        }

        $this->ideas = $sorted;
        return;
    }

    public function output()
    {
        foreach ($this->input as $key => $value) {
            print_r(implode('', $value));
            print_r(PHP_EOL);
        }
    }

    public function moveToDir($elf, $dir)
    {
        [$y, $x] = explode(':', $elf);

        switch ($dir) {
            case 'N':
                return $y - 1 . ':' . $x;
            case 'S':
                return $y + 1 . ':' . $x;
            case 'W':
                return $y . ':' . $x - 1;
            case 'E':
                return $y . ':' . $x + 1;
        }
    }

    public function isClearInDir($elf, $dir)
    {
        [$y, $x] = explode(':', $elf);

        switch ($dir) {
            case 'N':
                return !in_array($y - 1 . ':' . $x - 1, $this->elves)
                && !in_array($y - 1 . ':' . $x, $this->elves)
                && !in_array($y - 1 . ':' . $x + 1, $this->elves);
            case 'S':
                return !in_array($y + 1 . ':' . $x - 1, $this->elves)
                && !in_array($y + 1 . ':' . $x, $this->elves)
                && !in_array($y + 1 . ':' . $x + 1, $this->elves);
            case 'W':
                return !in_array($y - 1 . ':' . $x - 1, $this->elves)
                && !in_array($y . ':' . $x - 1, $this->elves)
                && !in_array($y + 1 . ':' . $x - 1, $this->elves);
            case 'E':
                return !in_array($y - 1 . ':' . $x + 1, $this->elves)
                && !in_array($y . ':' . $x + 1, $this->elves)
                && !in_array($y + 1 . ':' . $x + 1, $this->elves);
        }
    }

    public function isClearAround($elf)
    {
        [$y, $x] = explode(':', $elf);

        return !in_array($y - 1 . ':' . $x - 1, $this->elves)
        && !in_array($y - 1 . ':' . $x, $this->elves)
        && !in_array($y - 1 . ':' . $x + 1, $this->elves)
        && !in_array($y . ':' . $x - 1, $this->elves)
        && !in_array($y . ':' . $x + 1, $this->elves)
        && !in_array($y + 1 . ':' . $x - 1, $this->elves)
        && !in_array($y + 1 . ':' . $x, $this->elves)
        && !in_array($y + 1 . ':' . $x + 1, $this->elves);
    }

    public function mapElves()
    {
        foreach ($this->input as $row => $value) {
            foreach ($value as $col => $val) {
                if ($val == '#') {
                    $this->elves[] = $row . ':' . $col;
                }
            }
        }
    }
}
