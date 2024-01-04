<?php

namespace utils;

class FlagAdapter
{

    private $flag;

    public function getFlag($bitPos) {
        return (($this->flag & (1<<$bitPos)) != 0);
    }

    public function setFlag($bitPos, $flagState) {
        if ($flagState) {
            $this->flag |= 1<<$bitPos;
        } else {
            $this->flag &= ~(1<<$bitPos);
        }
    }

    public function fromInt($flag) {
        $this->flag = $flag;
    }

    public function toInt() {
        return $this->flag;
    }
}