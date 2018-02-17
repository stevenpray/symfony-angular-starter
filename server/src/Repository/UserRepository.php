<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use function strtolower;

/**
 * Class UserRepository
 *
 * @package App\Repository
 */
class UserRepository extends EntityRepository
{
    /**
     * @param string $username
     * @return User|null
     */
    public function findOneByUsername(string $username): ?User
    {
        return $this->findOneBy(['username' => strtolower($username)]);
    }
}
