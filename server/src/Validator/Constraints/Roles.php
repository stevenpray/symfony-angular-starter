<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Roles
 *
 * @package App\Validator\Constraints
 * @Annotation()
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Roles extends Constraint
{
    public const NO_SUCH_ROLE_ERROR = 'a3b01fa4-467f-443f-a54c-051d358d8411';

    /**
     * @var string
     */
    public $message = 'The role "{{ role }}" is not recognized.';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return RolesValidator::class;
    }
}
