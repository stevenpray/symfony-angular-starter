<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 *
 * @package App\Tests\Entity
 */
class UserTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testIsAccountNonExpired(): void
    {
        $user = new User();
        $this->assertTrue($user->isAccountNonExpired());
    }

    /**
     * @dataProvider provideLockableUsers
     * @param User $user
     * @param bool $expected
     */
    public function testIsAccountNonLocked(User $user, bool $expected): void
    {
        $this->assertSame($expected, $user->isAccountNonLocked());
    }

    /**
     * @dataProvider provideExpirableCredentialsUsers
     * @param User $user
     * @param bool $expected
     */
    public function testIsCredentialsNonExpired(User $user, bool $expected): void
    {
        $this->assertSame($expected, $user->isCredentialsNonExpired());
    }

    /**
     * @return array[]
     * @throws \Exception
     */
    public function provideLockableUsers(): array
    {
        $user = new User();
        return [
            [clone $user, true],
            [clone $user->setLocked(true), false],
            [clone $user->setLocked(false), true],
        ];
    }

    /**
     * @return array[]
     * @throws \Exception
     */
    public function provideExpirableCredentialsUsers(): array
    {
        $user = new User();
        return [
            [clone $user, true],
            [clone $user->setPasswordExpiresAt(new DateTime('yesterday')), false],
            [clone $user->setPasswordExpiresAt(new DateTime('tomorrow')), true],
            [clone $user->setPasswordExpiresAt(null), true],
        ];
    }
}
