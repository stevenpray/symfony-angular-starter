<?php
declare(strict_types=1);

namespace App\Repository;

use App\DBAL\Types\UserEventType;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class UserEventRepository
 *
 * @package App\Repository
 */
class UserEventRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findConsecutiveLoginFailureCountByUser(User $user): int
    {
        $types = [UserEventType::INTERACTIVE_LOGIN_SUCCESS, UserEventType::PASSWORD_RESET_SUCCESS];
        $success = $this->createQueryBuilder('user_event')
                        ->select('user_event')
                        ->where('user_event.user = :user')
                        ->andWhere('user_event.type IN (:types)')
                        ->setParameter('types', $types)
                        ->setParameter('user', $user)
                        ->orderBy('user_event.createdAt')
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();

        $qb = $this->createQueryBuilder('user_event')
                   ->select('count(user_event)')
                   ->where('user_event.type = :type')
                   ->setParameter('type', UserEventType::INTERACTIVE_LOGIN_FAILURE);
        if ($success) {
            $qb->andWhere('user_event.createdAt > :createdAt')
               ->setParameter('createdAt', $success->getCreatedAt());
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}
