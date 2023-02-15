<?php
namespace aoc2022;

Class Tetris {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_17/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_17/input_test.txt';

    protected $input;
    protected $instructions;
    protected $map = [];
    public static $mapHeight = 0;
    public static $horLimits = [
        'left' => 0,
        'right' => 6,
    ];
    protected $curDirIndex = -1;
    protected $curPiece;
    protected $flag = false;

    public function __construct(string $test)
    {
        $fileName = $test == '' ? self::FILE_PATH : self::TEST_FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);
        $this->instructions = str_split($this->input[0]);
        $this->run(130);
        print_r(Tetris::$mapHeight . PHP_EOL);

        // every 1740th piece
        // gains height of 2716 units

        // 1050 pieces ... 1740 x n pieces ... 130 pieces
        // 1643 height ... 2716 x n pieces ... 199 height ?? 215?
        // 1842 ja 1858 vahel

        $piecesInTheEnd = gmp_mod(gmp_sub(1000000000000, 1050), 1740);
        print_r($piecesInTheEnd);
        $divided = gmp_div(gmp_sub(gmp_sub(1000000000000, 1050), $piecesInTheEnd), 1740) ;

        $newHeight = gmp_add(gmp_mul($divided, 2716), 1857);
        print_r($newHeight);
        // 1560919540230 too low
        // 1560919540246 too high
        // 1514285714288

    }

    public function run($amount)
    {
        $p = new Piece();        
        $this->curPiece = $p;

        foreach (range(0, $amount) as $counter) {
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
                    $canMoveDown = false;
                }
            }

            $p = $p->getNext();

            if ($this->flag) {
                print_r('piece no: ' . $counter . PHP_EOL);
                $this->flag = false;
            }
        }
    }

    public function canBePlaced($p)
    {
        foreach ($p->loc as $key => $row) {
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

        Tetris::$mapHeight = count($this->map);

        if (isset(($this->map[Tetris::$mapHeight - 1])) && array_sum($this->map[Tetris::$mapHeight - 1]) == 7) {
            print_r(Tetris::$mapHeight . PHP_EOL);
            $this->flag = true;
        }
    }

    public function drawMap()
    {
        foreach (range(20,0) as $yvalue) {
            foreach (range(0,6) as $xvalue) {
                $map = isset($this->map[$yvalue][$xvalue]) ? '#' : '.';
                $piece = '';
                if (isset($this->curPiece->loc[$yvalue]) && in_array($xvalue, $this->curPiece->loc[$yvalue])) {
                    $piece = '@';
                }
                print_r($piece != '' ? $piece : $map);                
            }
            print_r(PHP_EOL);
        }
        print_r(PHP_EOL);
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
