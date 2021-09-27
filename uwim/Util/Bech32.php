<?php


namespace uwim\Util;

class Bech32
{
    private $alphabet = 'qpzry9x8gf2tvdw0s3jn54khce6mua7l';
    private $generator = [0x3b6a57b2, 0x26508e6d, 0x1ea119fa, 0x3d4233dd, 0x2a1462b3];


    /**
     * @param $bytes
     * @return string
     */
    public function encode($bytes)
    {
        $prefix = substr($bytes, 0, 2);
        $prefix = (ord($prefix[0]) << 8) + ord($prefix[1]);
        $prefix = chr(($prefix >> 10 & 0x1F) + 96) . chr(($prefix >> 5 & 0x1F) + 96);

        $data = $this->convert8to5(substr($bytes, 2, 32));

        $checksum = $this->createChecksum($prefix, $data);

        return "{$prefix}1{$data}{$checksum}";
    }

    public function decode(string $bech32): string
    {
        $bech32 = strtolower($bech32);
        $sepPos = strpos($bech32, '1');

        // decode prefix
        $prefix = substr($bech32, 0, $sepPos);
        $prefix = ((ord($prefix[0]) - 96) << 10) + ((ord($prefix[1]) - 96) << 5);
        $prefix = (chr($prefix >> 8 & 0xff) . chr($prefix & 0xff));

        // decode data
        $data = substr($bech32, ($sepPos + 1));
        $data = $this->convert5to8(substr($data, 0, -6));

        return $prefix . $data;
    }

    /**
     * @param string $data
     * @return string
     */
    private function convert5to8(string $data): string
    {
        $acc = 0;
        $bits = 0;
        $bytes = '';

        for ($i = 0, $l = strlen($data); $i < $l; $i++) {
            $acc = ($acc << 5) | (int)strpos($this->alphabet, $data[$i]);
            $bits += 5;

            while ($bits >= 8) {
                $bits -= 8;
                $bytes .= chr(($acc >> $bits) & 0xff);
            }
        }

        return $bytes;
    }

    /**
     * @param $bytes
     * @return string
     */
    private function convert8to5($bytes)
    {
        $acc = 0;
        $bits = 0;
        $res = '';

        for ($i = 0, $l = strlen($bytes); $i < $l; $i++) {
            $acc = ($acc << 8) | ord($bytes[$i]);
            $bits += 8;

            while ($bits >= 5) {
                $bits -= 5;
                $res .= $this->alphabet[(($acc >> $bits) & 0x1f)];
            }
        }

        if ($bits) {
            $res .= $this->alphabet[($acc << 5 - $bits) & 0x1f];
        }

        return $res;
    }

    /**
     * @param $prefix
     * @param $data
     * @return string
     */
    private function createChecksum($prefix, $data)
    {
        $values = array_merge(
            $this->prefixExpand($prefix),
            $this->strToBytes($data),
            array_fill(0, 6, 0)
        );
        $polyMod = $this->polyMod($values) ^ 1;

        $checksum = '';
        for ($i = 0; $i < 6; $i++) {
            $checksum .= $this->alphabet[($polyMod >> 5 * (5 - $i)) & 31];
        }

        return $checksum;
    }

    /**
     * @param $values
     * @return int
     */
    private function polyMod($values)
    {
        $chk = 1;
        for ($i = 0, $l = count($values); $i < $l; $i++) {
            $top = $chk >> 25;
            $chk = ($chk & 0x1ffffff) << 5 ^ $values[$i];

            for ($j = 0; $j < 5; $j++) {
                $value = (($top >> $j) & 1)
                    ? $this->generator[$j]
                    : 0;
                $chk ^= $value;
            }
        }

        return $chk;
    }

    /**
     * @param $prefix
     * @return array
     */
    private function prefixExpand($prefix)
    {
        $len = strlen($prefix);
        $res = array_fill(0, (($len * 2) + 1), 0);
        for ($i = 0; $i < $len; $i++) {
            $ord = ord($prefix[$i]);
            $res[$i] = $ord >> 5;
            $res[$i + $len + 1] = $ord & 31;
        }

        return $res;
    }

    /**
     * @param $data
     * @return int[]
     */
    private function strToBytes($data)
    {
        return array_map(
            function (string $chr) {
                return (int)strpos($this->alphabet, $chr);
            },
            str_split($data)
        );
    }
}
