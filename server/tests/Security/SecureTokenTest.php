<?php
declare(strict_types=1);

namespace App\Tests\Security;

use App\Security\SecureToken;
use PHPUnit\Framework\TestCase;

/**
 * Class SecureTokenTest
 *
 * @package App\Tests\Security
 * @covers \App\Security\SecureToken
 */
class SecureTokenTest extends TestCase
{
    /**
     * @dataProvider provideTokenLengths
     * @param int|null $length
     * @throws \Exception
     */
    public function testGenerate(?int $length): void
    {
        $token = SecureToken::generate($length);
        $this->assertEquals($length, \strlen($token));
    }

    /**
     * @dataProvider provideTokenLengths
     * @param int|null $length
     * @throws \Exception
     */
    public function testToString(?int $length): void
    {
        $token = new SecureToken($length);
        $this->assertEquals((string)$token, $token->__toString());
        $this->assertEquals($length, \strlen((string)$token));
    }

    /**
     * @return array
     */
    public function provideTokenLengths(): array
    {
        return [[0], [1], [31], [63]];
    }
}
