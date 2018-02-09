<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package App\Controller
 */
class DefaultController
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return new Response('Hello.', Response::HTTP_OK, ['Content-Type' => 'text/plain']);
    }
}
