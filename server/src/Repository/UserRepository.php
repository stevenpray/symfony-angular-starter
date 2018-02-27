<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use function strtolower;

/**
 * Class UserRepository
 *
 * @package App\Repository
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserEventRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function findOneByUsername(string $username): ?User
    {
        return $this->findOneBy(['username' => strtolower($username)]);
    }

    /**
     * @param $email
     * @return User|null
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => strtolower($email)]);
    }
}
