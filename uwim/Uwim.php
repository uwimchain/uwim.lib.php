<?php


namespace uwim;

use BitWasp\BitcoinLib\BIP39\BIP39;
use Exception;
use uwim\Util\Bech32;
use uwim\Util\Ed25519\Ed25519;

class Uwim
{
    private const ADDRESS_BYTES_LENGTH = 34;

    private const ADDRESS_LENGTH = 61;

    private const ADDRESS_PREFIXES = ["uw", "nd", "sc"];

    private const SECRET_KEY_LENGTH = 64;

    private const PUBLIC_KEY_LENGTH = 32;

    private const TRANSACTION_RAW_KEY = [139, 111, 224, 92, 142, 122, 138, 224, 138, 118, 30, 229, 209, 155, 193, 186,
        180, 234, 69, 249, 75, 71, 195, 105, 20, 61, 211, 13, 104, 253, 72, 5];

    private const TRANSACTION_RAW_IV = [22, 129, 2, 139, 42, 15, 11, 131, 158, 197, 170, 43, 114, 14, 178, 167];

    private const TRANSACTION_RAW_MAX_COMMENT_DATA_LENGTH = 8000;

    private const TRANSACTION_RAW_TYPES = [1, 3];

    /**
     * @return string
     * @throws Exception
     */
    public static function GenerateMnemonic()
    {
        try {
            $entropy = BIP39::generateEntropy();
        } catch (Exception $e) {
            throw $e;
        }

        try {
            return BIP39::entropyToMnemonic($entropy);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $mnemonic
     * @return mixed
     */
    public static function SeedFromMnemonic($mnemonic)
    {
        return hash_pbkdf2('sha512', $mnemonic, "", 2048, 32, true);
    }

    /**
     * @param $seed
     * @return string
     */
    public static function SecretKeyFromSeed($seed)
    {
        $ed25519 = new Ed25519();
        return $ed25519->secretKeyFromSeed($seed);
    }

    /**
     * @param $mnemonic
     * @return string
     */
    public static function SecretKeyFromMnemonic($mnemonic)
    {
        $ed25519 = new Ed25519();
        $seed = self::SeedFromMnemonic($mnemonic);
        return $ed25519->secretKeyFromSeed($seed);
    }

    /**
     * @param $secret_key
     * @return false|string
     * @throws Exception
     */
    public static function PublicKeyFromSecretKey($secret_key)
    {
        if (strlen($secret_key) != self::SECRET_KEY_LENGTH) throw new Exception("Invalid secret key length");
        return substr($secret_key, 32, self::PUBLIC_KEY_LENGTH);
    }

    /**
     * @param $mnemonic
     * @return false|string
     */
    public static function PublicKeyFromMnemonic($mnemonic)
    {
        $secret_key = self::SecretKeyFromMnemonic($mnemonic);
        return substr($secret_key, 32, self::PUBLIC_KEY_LENGTH);
    }

    /**
     * @param $public_key // Массив байтов публичного ключа
     * @param $prefix // Префикс адреса, одиниз трёх: "uw", "nd", "sc"
     * @return string
     * @throws Exception
     */
    public static function AddressFromPublicKey($public_key, $prefix)
    {
        if (strlen($prefix) != 2) throw new Exception("Invalid prefix length");
        if (!in_array($prefix, self::ADDRESS_PREFIXES)) throw new Exception("Invalid prefix data");
        if (strlen($public_key) != self::PUBLIC_KEY_LENGTH) throw new Exception("Invalid public key length");

        $BECH32 = new Bech32();

        $bytes = str_repeat("\x0", self::ADDRESS_BYTES_LENGTH);

        $prefix = (ord($prefix[0]) - 96 << 10) + (ord($prefix[1]) - 96 << 5);
        $prefix = (chr($prefix >> 8 & 0xff) . chr($prefix & 0xff));
        $bytes = substr_replace($bytes, $prefix, 0, 2);

        $bytes = substr_replace($bytes, $public_key, 2, 32);

        return $BECH32->encode($bytes);
    }

    /**
     * @param $mnemonic
     * @param $prefix // Префикс адреса, одиниз трёх: "uw", "nd", "sc"
     * @return string
     * @throws Exception
     */
    public static function AddressFromMnemonic($mnemonic, $prefix)
    {
        $public_key = self::PublicKeyFromMnemonic($mnemonic);
        try {
            return self::AddressFromPublicKey($public_key, $prefix);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $mnemonic string
     * @param $sender string
     * @param $recipient string
     * @param $amount float
     * @param $token_label string
     * @param $comment_title string
     * @param $comment_data string
     * @param $type int // 1,3
     * @return string
     * @throws Exception
     */
    public static function GetRawTransaction(string $mnemonic, string $sender, string $recipient, float $amount,
                                             string $token_label, string $comment_title, string $comment_data,
                                             int $type): string
    {
        if (!$sender || strlen($sender) != self::ADDRESS_LENGTH || !in_array(substr($sender, 0, 2), self::ADDRESS_PREFIXES)) throw new Exception("Invalid sender address data");
        if (!$recipient || strlen($recipient) != self::ADDRESS_LENGTH || !in_array(substr($recipient, 0, 2), self::ADDRESS_PREFIXES)) throw new Exception("Invalid recipient address data");

        if (!$mnemonic || count(explode(" ", $mnemonic)) != 24) throw new Exception("Invalid mnemonic data");

        if (strlen($comment_data) > self::TRANSACTION_RAW_MAX_COMMENT_DATA_LENGTH) throw new Exception("Too long transaction comment data");

        if (!in_array($type, self::TRANSACTION_RAW_TYPES)) throw new Exception("Unexpected transaction type");

        $ED25519 = new Ed25519();

        $tx = [
            "type" => $type,
            "nonce" => time() + rand(0, 1000000000000000000),
            "hashTx" => "",
            "height" => 0,
            "from" => $sender,
            "to" => $recipient,
            "amount" => $amount,
            "tokenLabel" => $token_label,
            "timestamp" => "",
            "tax" => 0,
            "signature" => null,
            "comment" => [
                "title" => $comment_title,
                "data" => base64_encode($comment_data),
            ]
        ];

        $json_string = json_encode($tx);

        $tx = [
            "type" => $type,
            "nonce" => $tx["nonce"],
            "from" => $sender,
            "to" => $recipient,
            "amount" => $amount,
            "tokenLabel" => $token_label,
            "signature" => base64_encode($ED25519->sign($json_string, self::SecretKeyFromMnemonic($mnemonic))),
            "comment" => [
                "title" => $comment_title,
                "data" => $comment_data,
            ]
        ];

        $json_string = json_encode($tx);

        if ($json_string) {
            return base64_encode(openssl_encrypt(
                $json_string,
                'AES-256-CBC',
                self::ByteArrToString(self::TRANSACTION_RAW_KEY),
                OPENSSL_RAW_DATA,
                self::ByteArrToString(self::TRANSACTION_RAW_IV)
            ));
        }

        return "";
    }

    /**
     * @param $string
     * @return array|false
     */
    public static function StingToByteArr($string): array
    {
        return unpack("C*", $string);
    }

    /**
     * @param $byte_array
     * @return array|false
     */
    public static function ByteArrToString($byte_array): string
    {
        $result = "";
        foreach ($byte_array as $item) {
            $result .= pack("C*", $item);
        }

        return $result;
    }
}
