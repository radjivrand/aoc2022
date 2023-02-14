<?php
namespace aoc2022;

Class Tetris {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_17/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_17/input_test.txt';

    protected $input;
    protected $instructions;
    protected $map = [];
    public static $mapHeight;
    public static $horLimits = [
        'left' => 0,
        'right' => 6,
    ];
    protected $curDirIndex = 0;

    public function __construct(string $test)
    {
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);
        $this->instructions = str_split($this->input[0]);

        $this->run();
    }

    public function run()
    {
        $p = new Piece();

        foreach (range(0,5) as $counter) {
            $canMoveDown = true;

            while ($canMoveDown) {
                $p->move($this->getNextDirection());

                if (!$this->canBePlaced($p)) {
                    $p->undoLastMove();
                }

                $p->moveDown();

                if (!$this->canBePlaced($p)) {
                    $p->undoLastMove();
                    $this->drop($p);
                    $this->drawMap();
                    $canMoveDown = false;
                    $p = $p->getNext();
                }
            }
        }
    }

    public function canBePlaced(Piece $piece)
    {
        foreach ($piece->loc as $key => $row) {
            if ($key < 0) {
                return false;
            }

            foreach ($row as $value) {
                if (
                    ($value > Tetris::$horLimits['right'] || $value < Tetris::$horLimits['left'])
                    || isset($this->map[$key][$value])
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    public function drop(Piece $piece)
    {
        foreach ($piece->loc as $rowKey => $row) {
            foreach ($row as $value) {
                $this->map[$rowKey][$value] = true;
            }
        }
    }

    public function drawMap()
    {
        foreach (range(10,0) as $yvalue) {
            foreach (range(0,6) as $xvalue) {
                print_r(isset($this->map[$yvalue][$xvalue]) ? '#' : '.');
            }
            print_r(PHP_EOL);
        }
    }

    public function getNextDirection()
    {
        if (count($this->instructions) - 1 == $this->curDirIndex) {
            $this->curDirIndex = 0;
            return $this->instructions[$this->curDirIndex];
        }

        $this->curDirIndex++;
        return $this->instructions[$this->curDirIndex];
    }
}
