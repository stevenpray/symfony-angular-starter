<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 *
 * @package App\Repository
 */
class UserRepository extends EntityRepository
{
    /**
     * @param string $username
     * @return null|User
     */
    public function findOneByUsername(string $username): ?User
    {
        return $this->findOneBy(['username' => strtolower($username)]);
    }
}
