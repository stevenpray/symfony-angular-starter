<?php
declare(strict_types=1);

namespace App\Security;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function array_map;
use function array_unique;
use function array_values;
use function in_array;
use function is_string;

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
        $roles = array_map(
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

    /**
     * @return string[]
     * @throws ReflectionException
     */
    public function getRoles(): array
    {
        $reflection = new ReflectionClass($this->hierarchy);
        $property = $reflection->getProperty('hierarchy');
        $property->setAccessible(true);
        $hierarchy = $property->getValue($this->hierarchy);
        $roles = [];
        $aIterator = new RecursiveArrayIterator($hierarchy);
        $iIterator = new RecursiveIteratorIterator($aIterator, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iIterator as $key => $value) {
            if (is_string($key)) {
                $roles[] = $key;
            }
            if (is_string($value)) {
                $roles[] = $value;
            }
        }

        return array_unique($roles);
    }
}
