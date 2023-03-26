<?php
namespace aoc2022;

Class Laby {
    //You guessed 361, too low – but right for someone?
    //You guessed 464, too high
    //You guessed 463, too high
    //You guessed 457, just wrong
    //You guessed 455, also wrong

    // 456 it is!

    // vol2: 435: too low
    // vol2: 455: too high

    const FILE_PATH = '/Users/arne/dev/aoc2022/input_12/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_12/input_test.txt';

    protected $input;
    protected $lines;

    protected $maxx;
    protected $maxy;
    protected $minx = 0;
    protected $miny = 0;

    protected $start;
    protected $end;

    protected $queue = [];
    protected $visited = [];
    protected $parent = [];

    public function __construct(string $test)
    {
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);
        $this->createMapFromInput();

        $this->initalize();
        $this->initStartFirstPart();
        $this->initStartSecondPart();

        // part II
        $results = [];
        foreach ($this->lines as $key => &$line) {
            $this->queue = [];
            $this->visited = [];
            $this->parent = [];

            $this->start = $key . '-0';
            $line[0] = '`';

            if ($key > 0) {
                $this->lines[$key - 1][0] = 'a';
            }

            $res = $this->run();
            print_r(count($res) . PHP_EOL);
            $results[] = $res;
            $this->drawResult($res);
        }

        // part I
        // $res = $this->run();
        // print_r(count($res));
    }

    public function drawResult($result)
    {
        foreach (range(0, $this->maxx) as $xvalue) {
            foreach (range(0, $this->maxy) as $yvalue) {
                $dot = in_array($xvalue . '-' . $yvalue, $result);
                print_r($dot ? strtoupper($this->lines[$xvalue][$yvalue]) : $this->lines[$xvalue][$yvalue]);

            }
            print_r(PHP_EOL);
        }
    }

    public function initStartFirstPart()
    {
        foreach ($this->lines as $lkey => &$line) {
            foreach ($line as $key => &$value) {
                if ($value == 'S') {
                    $this->start = $lkey . '-' . $key;
                    $value = '`';
                }
            }
        }
    }

    public function initStartSecondPart()
    {
        $values = explode('-', $this->start);
        $this->lines[$values[0]][$values[1]] = 'a';
    }

    public function initalize()
    {
        $this->maxx = count($this->lines) - 1;
        $this->maxy = count($this->lines[0]) - 1;

        foreach ($this->lines as $lkey => &$line) {
            foreach ($line as $key => &$value) {
                if ($value == 'E') {
                    $this->end = $lkey . '-' . $key;
                    // print_r('koht: ' . $lkey . ' ja ' . $key . PHP_EOL);
                    $value = '{';
                }
            }
        }
    }

    public function createMapFromInput()
    {
        foreach ($this->input as $line) {
            $splitLine = str_split($line);
            $this->lines[] = $splitLine;
        }
    }

    public function run()
    {
        $this->visited[] = $this->start;
        $adjacent = $this->findNeighbours($this->start) ?? [];
        $this->queue[] = $this->start;

        while (!in_array($this->end, $this->visited)) {
            $node = array_shift($this->queue);
            $adjacent = $this->findNeighbours($node);

            foreach ($adjacent as $neigh) {
                if (!in_array($neigh, $this->visited)
                    && (
                        ord($this->getVal($neigh)) - ord($this->getVal($node)) <= 1
                        // || ord($this->getVal($neigh)) - ord($this->getVal($node)) == 0
                    )
                ) {
                    $this->parent[$neigh] = $node;
                    $this->visited[] = $neigh;
                    $this->queue[] = $neigh;
                }
            }
        }

        $path = [];

        while ($node != $this->start) {
            $path[] = $node;
            $node = $this->parent[$node];
        }

        $path[] = $this->start;
        return $path;
    }

    public function getVal(string $address)
    {
        $values = explode('-', $address);
        return $this->lines[$values[0]][$values[1]];
    }

    public function findNeighbours($address)
    {
        $val = explode('-', $address);
        $up = $val[1] - 1;
        $down = $val[1] + 1;
        $left = $val[0] - 1;
        $right = $val[0] + 1;

        $res = [];

        if ($up >= $this->miny) {
            $new = $val[0] . '-' . $up;
            $res[] = $new;
        }

        if ($down <= $this->maxy) {
            $new = $val[0] . '-' . $down;
            $res[] = $new;
        }

        if ($left >= $this->minx) {
            $new = $left . '-' . $val[1];
            $res[] = $new;
        }

        if ($right <= $this->maxx) {
            $new = $right . '-' . $val[1];
            $res[] = $new;
        }

        return $res;
    }
}
