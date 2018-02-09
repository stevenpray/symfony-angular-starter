<?php
declare(strict_types=1);

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Class UserEventTypeEnumType
 *
 * @package App\DBAL\Types\Sim
 */
class UserEventType extends AbstractEnumType
{
    public const CREATE                    = 'create_user';
    public const DELETE                    = 'delete_user';
    public const USERNAME_REQUEST          = 'username_request';
    public const PASSWORD_RESET_REQUEST    = 'password_reset_request';
    public const PASSWORD_RESET_SUCCESS    = 'password_reset_success';
    public const INTERACTIVE_LOGIN_SUCCESS = 'interactive_login_success';
    public const INTERACTIVE_LOGIN_FAILURE = 'interactive_login_failure';

    /**
     * {@inheritdoc}
     */
    protected static $choices = [
        self::CREATE                    => 'Create User',
        self::DELETE                    => 'Delete User',
        self::USERNAME_REQUEST          => 'Username Request',
        self::PASSWORD_RESET_REQUEST    => 'Password Reset Request',
        self::PASSWORD_RESET_SUCCESS    => 'Password Reset Success',
        self::INTERACTIVE_LOGIN_SUCCESS => 'Interactive Login Success',
        self::INTERACTIVE_LOGIN_FAILURE => 'Interactive Login Failure',
    ];

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'user_event_type';
    }
}
