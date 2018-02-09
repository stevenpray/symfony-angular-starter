<?php
declare(strict_types=1);

namespace App\Tests\Security;

use App\Security\SecureToken;
use PHPUnit\Framework\TestCase;

/**
 * Class SecureTokenTest
 *
 * @package App\Tests\Security
 */
class SecureTokenTest extends TestCase
{
    public function testGenerate()
    {
        $token = SecureToken::generate();
        self::assertNotNull($token);
        self::assertEquals(31, \strlen($token));
    }
}
