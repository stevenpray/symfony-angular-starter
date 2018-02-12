<?php
declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\UserProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Class UserProviderTest
 *
 * @package App\Tests\Security
 * @covers \App\Security\UserProvider
 */
class UserProviderTest extends TestCase
{
    /**
     * @var UserProvider
     */
    protected $provider;

    /**
     * @var User
     */
    protected $user;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->user = new User();
        $this->user->setUsername('user');

        $er = $this->getMockBuilder(EntityRepository::class)
                   ->disableOriginalConstructor()
                   ->setMethods(['find', 'findOneBy'])
                   ->getMock();

        $er
           ->method('find')
           ->with($this->isType('integer'))
           ->will(
               $this->returnCallback(
                   function ($id) {
                       return $id === 1 ? $this->user : null;
                   }
               )
           );

        $er
           ->method('findOneBy')
           ->with($this->isType('array'))
           ->will(
               $this->returnCallback(
                   function ($criteria) {
                       return $criteria['username'] === $this->user->getUsername() ? $this->user : null;
                   }
               )
           );

        $em = $this->getMockBuilder(EntityManager::class)
                   ->disableOriginalConstructor()
                   ->setMethods(['getRepository'])
                   ->getMock();

        $em->method('getRepository')
           ->with(User::class)
           ->willReturn($er);

        /** @var EntityManagerInterface $em */
        $this->provider = new UserProvider($em);
    }

    /**
     * @dataProvider provideUserIds
     * @param int $id
     * @param bool $expected
     */
    public function testLoadUserById(int $id, bool $expected): void
    {
        if (!$expected) {
            $this->expectException(UsernameNotFoundException::class);
        }
        $result = $this->provider->loadUserById($id);
        if ($expected) {
            $this->assertSame(User::class, \get_class($result));
        }
    }

    /**
     * @dataProvider provideUsernames
     * @param string $username
     * @param bool $expected
     */
    public function testLoadUserByUsername(string $username, bool $expected): void
    {
        if (!$expected) {
            $this->expectException(UsernameNotFoundException::class);
        }
        $result = $this->provider->loadUserByUsername($username);
        if ($expected) {
            $this->assertSame(User::class, \get_class($result));
        }
    }

    public function testRefreshUser(): void
    {
        $this->expectException(UnsupportedUserException::class);
        $this->provider->refreshUser($this->user);
    }

    public function testSupportsClass(): void
    {
        $result = $this->provider->supportsClass(User::class);
        $this->assertTrue($result);

        $result = $this->provider->supportsClass('ShouldReturnFalse');
        $this->assertFalse($result);
    }

    /**
     * @return array[]
     */
    public function provideUserIds(): array
    {
        return [
            [0, false],
            [1, true],
        ];
    }

    /**
     * @return array[]
     */
    public function provideUsernames(): array
    {
        return [
            ['user', true],
            ['not_a_user', false],
        ];
    }
}
