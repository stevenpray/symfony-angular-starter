<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class UserRepositoryTest
 *
 * @package App\Tests\Repository
 * @covers \App\Repository\UserRepository
 */
class UserRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }

    /**
     * @dataProvider provideUsernames
     * @param null|string $username
     */
    public function testFindOneByUsername(?string $username, $expected): void
    {
        $user = $this->em->getRepository(User::class)->findOneByUsername($username);
        if ($expected) {
            $this->assertInstanceOf(User::class, $user);
        } else {
            $this->assertNull($user);
        }
    }

    /**
     * @return array[]
     */
    public function provideUsernames(): array
    {
        return [
            ['super', true],
            ['admin', true],
            ['user', true],
            ['non_existing_username', false],
        ];
    }
}
