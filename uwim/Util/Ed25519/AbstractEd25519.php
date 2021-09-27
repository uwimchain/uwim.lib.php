<?php


namespace uwim\Util\Ed25519;

abstract class AbstractEd25519 extends AbstractBase
{
    protected function cswap(&$p, &$q, $b)
    {
        for ($i = 0; $i < 4; $i++) {
            $this->sel25519($p[$i], $q[$i], $b);
        }
    }


    protected function inv25519(&$o, $i)
    {
        $c = $i;
        for ($a = 253; $a >= 0; $a--) {
            $this->fnM($c, $c, $c);
            if ($a != 2 && $a != 4) {
                $this->fnM($c, $c, $i);
            }
        }
        $o = $c;
    }


    protected function neq25519($a, $b)
    {
        $c = $d = str_repeat("\x0", 32);

        $this->pack25519($c, $a);
        $this->pack25519($d, $b);

        return $this->cryptoVerify32($c, $d);
    }


    protected function pack25519(&$o, $n)
    {
        $m = array_fill(0, 16, 0);
        $t = $n;

        $this->car25519($t);
        $this->car25519($t);
        $this->car25519($t);

        for ($j = 0; $j < 2; $j++) {
            $m[0] = $t[0] - 0xffed;
            for ($i = 1; $i < 15; $i++) {
                $m[$i] = $t[$i] - 0xffff - (($m[$i - 1] >> 16) & 1);
                $m[$i - 1] &= 0xffff;
            }
            $m[15] = $t[15] - 0x7fff - (($m[14] >> 16) & 1);
            $b = ($m[15] >> 16) & 1;
            $m[14] &= 0xffff;
            $this->sel25519($t, $m, 1 - $b);
        }

        for ($i = 0; $i < 16; $i++) {
            $o[2 * $i] = chr($t[$i] & 0xff);
            $o[2 * $i + 1] = chr($t[$i] >> 8);
        }
    }


    protected function par25519($a)
    {
        $d = str_repeat("\x0", 32);
        $this->pack25519($d, $a);

        return ord($d[0]) & 1;
    }


    protected function pow2523(&$o, $i)
    {
        $c = $i;

        for ($a = 250; $a >= 0; $a--) {
            $this->fnM($c, $c, $c);
            if ($a != 1) {
                $this->fnM($c, $c, $i);
            }
        }

        $o = $c;
    }


    protected function set25519(&$r, $a)
    {
        for ($i = 0; $i < 16; $i++) {
            $r[$i] = $a[$i];
        }
    }


    protected function unpack25519(&$o, $n)
    {
        for ($i = 0; $i < 16; $i++) {
            $o[$i] = ord($n[2 * $i]) + (ord($n[2 * $i + 1]) << 8);
        }
        $o[15] &= 0x7fff;
    }


    private function sel25519(&$p, &$q, $b)
    {
        $c = ~($b - 1);
        for ($i = 0; $i < 16; $i++) {
            $ttt = $c & ($p[$i] ^ $q[$i]);
            $p[$i] ^= $ttt;
            $q[$i] ^= $ttt;
        }
    }
}
