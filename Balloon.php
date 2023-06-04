<?php
namespace aoc2022;

use Exception;

Class Balloon {
    const FILE_PATH = '/Users/arne/dev/aoc2022/input_25/input.txt';
    const TEST_FILE_PATH = '/Users/arne/dev/aoc2022/input_25/input_test.txt';

    protected $input;
    protected $test;
    protected $longest;
    protected $intervals = [];

    const MAP = [
        '2' => 2,
        '1' => 1,
        '0' => 0,
        '-' => -1,
        '=' => -2,
    ];

    public function __construct(string $test)
    {
        $this->test = $test != '';
        $fileName = $this->test ? self::TEST_FILE_PATH : self::FILE_PATH;
        $this->input = file($fileName, FILE_IGNORE_NEW_LINES);

        $this->makeEven();
        $this->getIntervals();

        $sum = 0;
        foreach ($this->input as $key => $value) {
            $sum += $this->toInt($value);
        }

        // print_r([$sum]);
        // $this->getRes();

        // print_r([$this->toInt('-121-')]);
        // print_r($this->toSnafu(8923));
        // print_r($this->getDigits(578));

        // print_r($this->intervals);


        print_r($this->toSnafu(446));

        /**
         * 
         * 2679
         * 6 kohta
         * ehk alustame 7812
         * 2679 - 1 * 3125 = -446 (1)
         * -446 + 1 * 625 = 179 (-)
         * 179 - 125 = 54 (1)
         * 54 - 2.* 25 = 4 (2)
         * 4 - 5 = -1 (1)
         * -1 - 1 = (-)
         * 
         *          
         **/

        // print_r($this->add('12', '1'));
    }

    public function toSnafu(int $val, int $power = 0)
    {
        if ($power == 0) {
            $power = $this->findInterval(abs($val));
        }

        $curMul = 5 ** $power;
        print_r([abs($val)]);
        print_r([$curMul]);
        print_r([5 ** ($power - 1)]);

        while (abs($val) > 5 ** ($power - 1)) {
            if ($val > 0) {
                $val = $val - $curMul;
            } else {
                $val = $val + $curMul;
            }
            print_r([$val]);
        }

        print_r($val);




        // return (2 * (5 ** $digits - 1)) % $val;
    }

    public function findInterval(int $val)
    {
        foreach ($this->intervals as $key => $interval) {
            if ($val > $interval) {
                continue;
            }
            return $key;
        }
    }

    public function getIntervals()
    {
        $reference = 0;
        $power = 0;

        foreach (range(0, 10) as $power) {
            $reference += (5 ** $power) * 2;
            $this->intervals[$power] = $reference;
        }
    }

    public function addSnafus(string $a, string $b)
    {
        $carry = 0;
        $result = [];

        $a = str_split($a);
        $b = str_split($b);

        if (count($a) != count($b)) {
            throw new Exception("Uneven values", 1);
        }

        while (count($a) && count($b)) {
            $x = array_pop($a);
            $y = array_pop($b);

            $res = $this::MAP[$x]
            + $this::MAP[$y]
            + $carry;

            if ($res > 2) {
                $carry = 1;
                $snafu = array_flip($this::MAP)[$res - 5];
            } elseif ($res < -2) {
                $carry = -1;
                $snafu = array_flip($this::MAP)[$res + 5];
            } else {
                $carry = 0;
                $snafu = array_flip($this::MAP)[$res];
            }

            $result[] = $snafu;
        }

        if ($carry > 0) {
            $result[] = 1;
        }

        return  implode('', array_reverse($result));
    }

    public function getRes()
    {
        $res = str_repeat('0', $this->longest);

        foreach ($this->input as $value) {
            $res = $this->addSnafus($res, $value);
        }

        print_r($res);
    }

    public function toInt(string $snafu)
    {
        $arr = str_split($snafu);
        $res = 0;

        foreach (array_reverse($arr) as $key => $value) {
            $res += 5 ** $key * $this::MAP[$value];
        }

        return $res;
    }

    public function makeEven()
    {
        $this->longest = 0;

        foreach ($this->input as $value) {
            if (strlen($value) > $this->longest) {
                $this->longest = strlen($value);
            }
        }

        foreach ($this->input as &$value) {
            $diff = $this->longest - strlen($value);

            for ($i=0; $i < $diff; $i++) { 
                $value = '0' . $value;
            }
        }
    }
}
