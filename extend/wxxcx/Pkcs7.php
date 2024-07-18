<?php

namespace wxxcx;

class Pkcs7
{
    public function encode($text)
    {
        $amountToPad = 32 - (strlen($text) % 32);
        if ($amountToPad == 0) {
            $amountToPad = 32;
        }
        return $text . str_repeat(chr($amountToPad), $amountToPad);
    }

    public function decode($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }
}
