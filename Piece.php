<?php
namespace aoc2022;

use aoc2022\Tetris;

Class Piece {

    const SHAPES = ['_', '+', 'L', '|', 'cube'];

    const LOCATION = [
        '_' => [
            0 => [2,3,4,5],
        ],
        '+' => [
            0 => [3],
            1 => [2,3,4],
            2 => [3],
        ],
        'L' => [
            0 => [2,3,4],
            1 => [4],
            2 => [4],
        ],
        '|' => [
            0 => [2],
            1 => [2],
            2 => [2],
            3 => [2],
        ],
        'cube' => [
            0 => [2,3],
            1 => [2,3],
        ],
    ];

    protected $label;
    public $loc;
    public $previousLoc;

    public function __construct(string $label = '_')
    {
        $this->label = $label;
        $startingLocation = self::LOCATION[$this->label];
        $newLocation = [];
        foreach ($startingLocation as $key => $value) {
            $this->loc[$key + Tetris::$mapHeight + 3] = $value;
        }
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getNext()
    {
        $nextLabel = array_search($this->label, self::SHAPES);
        $nextLabel += $nextLabel == 4 ? -4 : 1;

        return new Piece($this::SHAPES[$nextLabel]);
    }

    public function moveDown()
    {
        $newLoc = [];
        foreach ($this->loc as $key => $row) {
            $newLoc[$key - 1] = $row;
        }
        $this->loc = $newLoc;
        return $this;
    }

    public function move(string $direction)
    {
        $this->previousLoc = $this->loc;
        foreach ($this->loc as $key => &$row) {
            foreach ($row as &$value) {
                $value += $direction == '>' ? 1 : -1;
            }
        }

        return $this;
    }

    public function undoLastMove()
    {
        $this->loc = $this->previousLoc;
        return $this;
    }
}
