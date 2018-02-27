<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use function json_decode;
use function json_encode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SignupController
 *
 * @package App\Controller
 * @Route("/signup")
 */
class SignupController
{
    /**
     * @Route("")
     * @Method("POST")
     * @return Response
     */
    public function index(): Response
    {
        return new JsonResponse();
    }

    /**
     * @Route("/{token}")
     * @Method("POST")
     * @return Response
     */
    public function confirm(): Response
    {
        return new JsonResponse();
    }

    /**
     * @Route("/check-username/{username}")
     * @Method("GET")
     * @param string $username
     * @param UserRepository $repository
     * @return Response
     */
    public function checkUsername(string $username, UserRepository $repository): Response
    {
        if ($repository->findOneByUsername($username)) {
            throw new ConflictHttpException();
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/check-email/{email}")
     * @Method("GET")
     * @param string $email
     * @param UserRepository $repository
     * @return Response
     */
    public function checkEmail(string $email, UserRepository $repository): Response
    {
        if ($repository->findOneByEmail($email)) {
            throw new ConflictHttpException();
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/validate/email")
     * @Method("POST")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function validateEmail(Request $request, ValidatorInterface $validator): Response
    {
        $email = json_decode($request->getContent(), true);
        $contraint = new EmailConstraint(['strict' => true, 'checkHost' => true, 'checkMX' => true]);
        $violations = $validator->validate($email, $contraint);
        if ($violations->count()) {
            throw new BadRequestHttpException();
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
