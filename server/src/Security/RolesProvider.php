<?php
declare(strict_types=1);

namespace App\Security;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function array_map;
use function array_unique;
use function array_values;
use function in_array;

/**
 * Class RolesProvider
 *
 * @package App\Security
 */
class RolesProvider
{
    protected const ROLE_USER              = 1;
    protected const ROLE_ADMIN             = 1 << 1 | self::ROLE_USER;
    protected const ROLE_ALLOWED_TO_SWITCH = 1 << 2 | self::ROLE_USER;
    protected const ROLE_SUPER_ADMIN       = self::ROLE_USER | self::ROLE_ADMIN | self::ROLE_ALLOWED_TO_SWITCH;

    /**
     * @var RoleHierarchyInterface
     */
    protected $hierarchy;

    /**
     * RolesProvider constructor.
     *
     * @param RoleHierarchyInterface $hierarchy
     */
    public function __construct(RoleHierarchyInterface $hierarchy)
    {
        $this->hierarchy = $hierarchy;
    }

    /**
     * @param UserInterface $user
     * @return int
     * @throws ReflectionException
     */
    public function getReachableRolesBitmask(UserInterface $user): int
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $class = new ReflectionClass(static::class);
        $bitmask = 0;
        foreach ($this->getReachableRoles($user) as $role) {
            $bitmask |= $class->getConstant($role);
        }

        return $bitmask;
    }

    /**
     * @param UserInterface $user
     * @return string[]
     */
    public function getReachableRoles(UserInterface $user): array
    {
        /** @var Role[] $roles */
        $roles = array_map(
            function ($role) {
                return new Role($role);
            },
            $user->getRoles()
        );
        $roles = $this->hierarchy->getReachableRoles($roles);
        /** @var string[] $roles */
        $roles = \array_map(
            function (Role $role) {
                return $role->getRole();
            },
            $roles
        );
        $roles = array_values(array_unique($roles));

        return $roles;
    }

    /**
     * @param UserInterface $user
     * @param string $role
     * @return bool
     */
    public function userHasRole(UserInterface $user, string $role): bool
    {
        return in_array($role, $this->getReachableRoles($user), true);
    }
}
