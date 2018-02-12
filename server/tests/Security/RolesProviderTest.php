<?php
declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\RolesProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

/**
 * Class RolesProviderTest
 *
 * @package App\Tests\Security
 */
class RolesProviderTest extends TestCase
{
    /**
     * @var RolesProvider
     */
    protected $rolesProvider;

    /**
     * @var User
     */
    protected $user;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $hierarchy = [
            'ROLE_ADMIN'       => [
                'ROLE_USER',
            ],
            'ROLE_SUPER_ADMIN' => [
                'ROLE_ADMIN',
                'ROLE_ALLOWED_TO_SWITCH',
            ],
        ];
        $this->rolesProvider = new RolesProvider(new RoleHierarchy($hierarchy));
        $this->user = new User();
        $this->user->addRole('ROLE_ADMIN');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetReachableRolesBitmask(): void
    {
        $result = $this->rolesProvider->getReachableRolesBitmask($this->user);
        $this->assertEquals(3, $result);
    }

    /**
     * @dataProvider provideUserRoles
     * @param string $role
     * @param bool $expected
     */
    public function testGetReachableRoles(string $role, bool $expected): void
    {
        $result = $this->rolesProvider->getReachableRoles($this->user);
        $this->assertCount(2, $result);
        if ($expected) {
            $this->assertContains($role, $result);
        } else {
            $this->assertNotContains($role, $result);
        }
    }

    /**
     * @dataProvider provideUserRoles
     * @param string $role
     * @param boolean $expected
     */
    public function testUserHasRole(string $role, bool $expected): void
    {
        $result = $this->rolesProvider->userHasRole($this->user, $role);
        $this->assertSame($expected, $result);
    }

    /**
     * @return array[]
     */
    public function provideUserRoles(): array
    {
        return [
            ['ROLE_USER', true],
            ['ROLE_ADMIN', true],
            ['ROLE_ALLOWED_TO_SWITCH', false],
            ['ROLE_NON_EXISTENT_ROLE', false],
        ];
    }
}
