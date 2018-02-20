<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Security\RolesProvider;
use ReflectionException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use function array_map;

/**
 * Class RolesValidator
 *
 * @package App\Validator\Constraints
 */
class RolesValidator extends ConstraintValidator
{
    /**
     * @var string[]
     */
    protected $roles = [];

    /**
     * RolesValidator constructor.
     *
     * @param RolesProvider $provider
     * @throws ReflectionException
     */
    public function __construct(RolesProvider $provider)
    {
        $this->roles = $provider->getRoles();
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExecutionContextInterface $context): void
    {
        parent::initialize($context);
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        /** @var Roles $constraint */
        $diff = array_diff(array_map('strtoupper', (array)$value), $this->roles);
        foreach ($diff as $role) {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ role }}', $role)
                          ->setCode(Roles::NO_SUCH_ROLE_ERROR)
                          ->addViolation();
        }
    }
}
