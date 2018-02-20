<?php
declare(strict_types=1);

namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\Roles;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RolesValidatorTest
 *
 * @package App\Tests\Validator\Constraints
 * @covers \App\Validator\Constraints\RolesValidator
 */
class RolesValidatorTest extends KernelTestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
    }

    /**
     * @dataProvider provideRoles
     * @param string|string[] $roles
     * @param int $expected
     */
    public function testValidate($roles, int $expected): void
    {
        $validator = $this->container->get('validator');
        $violations = $validator->validate($roles, new Roles());

        $this->assertEquals($violations->count(), $expected);
    }

    /**
     * @return array[]
     */
    public function provideRoles(): array
    {
        return [
            ['ROLE_UseR', 0],
            [['ROLE_USER'], 0],
            [['ROLE_user', 'ROLE_ADMIN'], 0],
            ['NON_EXISTING_ROLE', 1],
            [['NON_EXISTING_ROLE'], 1],
            [['NON_EXISTING_ROLE', 'role_admin'], 1],
            [['NON_EXISTING_ROLE', 'ANOTHER_NON_EXISTING_ROLE'], 2],
        ];
    }
}
