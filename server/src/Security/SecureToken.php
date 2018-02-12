<?php
declare(strict_types=1);

namespace App\Security;

/**
 * Class SecureToken
 *
 * @package App\Security
 */
class SecureToken
{
    protected const LENGTH                        = 31;
    protected const REGEX_ALPHANUM                = '/[^A-Za-z0-9]/';
    protected const REGEX_PRINTABLE               = '/[\x00-\x1F\x80-\xFF]/';
    protected const REGEX_PRINTABLE_NO_WHITESPACE = '/[^\x21-\x7E]/';

    /**
     * @var string
     */
    protected $value;

    /**
     * SecureToken constructor.
     *
     * @param int $length
     * @param string $pattern
     * @throws \Exception
     */
    public function __construct(int $length = self::LENGTH, string $pattern = self::REGEX_ALPHANUM)
    {
        $this->value = self::generate($length, $pattern);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param int $length
     * @param string $pattern
     * @return string
     * @throws \Exception
     */
    public static function generate(int $length = self::LENGTH, string $pattern = self::REGEX_ALPHANUM): string
    {
        $token = '';
        while (\strlen($token) < $length) {
            $bytes = random_bytes(10);
            $token .= preg_replace($pattern, null, $bytes);
        }
        $token = substr($token, 0, $length);

        return $token;
    }
}
