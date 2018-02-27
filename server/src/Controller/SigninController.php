<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SigninController
 *
 * @package App\Controller
 */
class SigninController
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return new JsonResponse();
    }

    /**
     * @return Response
     */
    public function password(): Response
    {
        return new JsonResponse();
    }

    /**
     * @return Response
     */
    public function username(): Response
    {
        return new JsonResponse();
    }
}
