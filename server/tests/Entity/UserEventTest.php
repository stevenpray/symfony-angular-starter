<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\UserEvent;
use PHPUnit\Framework\TestCase;

/**
 * Class UserEventTest
 *
 * @package App\Tests\Entity
 */
class UserEventTest extends TestCase
{

    public function testSetUser(): void
    {
        $user = new User();
        $event = new UserEvent();
        $event->setUser($user);
        $this->assertSame($user, $event->getUser());
        $this->assertTrue($user->hasUserEvent($event));
    }
}
