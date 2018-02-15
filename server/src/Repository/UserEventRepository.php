<?php
declare(strict_types=1);

namespace App\Repository;

use App\DBAL\Types\UserEventType;
use App\Entity\User;
use App\Entity\UserEvent;
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
     * @return UserEvent[]
     * @throws NonUniqueResultException
     */
    public function findConsecutiveLoginFailuresByUser(User $user): array
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
                   ->select('user_event')
                   ->where('user_event.type = :type')
                   ->setParameter('type', UserEventType::INTERACTIVE_LOGIN_FAILURE);
        if ($success) {
            $qb->andWhere('user_event.createdAt > :createdAt')
               ->setParameter('createdAt', $success->getCreatedAt());
        }

        return $qb->getQuery()->getResult();
    }
}
