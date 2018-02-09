<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 *
 * @package App\Security
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $id
     * @return UserInterface
     */
    public function loadUserById(int $id): UserInterface
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            throw new UsernameNotFoundException('Username not found.');
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => strtolower($username)]);
        if (!$user) {
            throw new UsernameNotFoundException('Username not found.');
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user): void
    {
        // Throwing this exception is proper to make things stateless.
        throw new UnsupportedUserException('Refresh not supported.');
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class): bool
    {
        return $class === User::class;
    }
}
