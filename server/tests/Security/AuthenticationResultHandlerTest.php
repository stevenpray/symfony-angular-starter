<?php
declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\AuthenticationResultHandler;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class AuthenticationResultHandlerTest
 *
 * @package App\Tests\Security
 * @covers \App\Security\AuthenticationResultHandler
 */
class AuthenticationResultHandlerTest extends TestCase
{

    public function testOnAuthenticationFailure(): void
    {
        /**
         * @var Request $request
         * @var TokenInterface $token
         * @var EntityManagerInterface $em
         * @var JWTTokenManagerInterface $jwtManager
         * @var EventDispatcherInterface $dispatcher
         */
        $request = $this->buildRequest();
        $em = $this->buildEntityManager();
        $jwtManager = $this->buildJWTManager('secrettoken');
        $dispatcher = $this->buildDispatcher();

        $handler = new AuthenticationResultHandler($em, $dispatcher, $jwtManager);
        $response = $handler->onAuthenticationFailure($request, new AuthenticationException());
        $content = json_decode($response->getContent(), true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(401, $content['code']);
        $this->assertEquals('Bad credentials', $content['message']);
    }

    public function testOnAuthenticationSuccess(): void
    {
        /**
         * @var Request $request
         * @var TokenInterface $token
         * @var EntityManagerInterface $em
         * @var JWTTokenManagerInterface $jwtManager
         * @var EventDispatcherInterface $dispatcher
         */
        $request = $this->buildRequest();
        $token = $this->buildToken();
        $em = $this->buildEntityManager();
        $jwtManager = $this->buildJWTManager('secrettoken');
        $dispatcher = $this->buildDispatcher();

        $handler = new AuthenticationResultHandler($em, $dispatcher, $jwtManager);
        $response = $handler->onAuthenticationSuccess($request, $token);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $content);
        $this->assertEquals('secrettoken', $content['token']);
    }

    /**
     * @return MockObject
     */
    public function buildRequest(): MockObject
    {
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();

        return $request;
    }

    /**
     * @return MockObject
     */
    public function buildToken(): MockObject
    {
        $token = $this->getMockBuilder(JWTUserToken::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $token->expects($this->any())
              ->method('getUser')
              ->will($this->returnValue($this->buildUser()));

        return $token;
    }

    /**
     * @return MockObject
     */
    public function buildUser(): MockObject
    {
        $user = $this->getMockBuilder(User::class)
                     ->setMethods(['getUsername', 'addUserEvent'])
                     ->getMock();
        $user->method('getUsername')
             ->will($this->returnValue('username'));
        $user->method('addUserEvent')
             ->will($this->returnValue($user));

        return $user;
    }

    /**
     * @param string|null $token
     * @return MockObject
     */
    public function buildJWTManager(string $token = null): MockObject
    {
        $jwtManager = $this->getMockBuilder(JWTManager::class)
                           ->disableOriginalConstructor()
                           ->getMock();
        if (null !== $token) {
            $jwtManager->expects($this->any())
                       ->method('create')
                       ->will($this->returnValue('secrettoken'));
        }

        return $jwtManager;
    }

    /**
     * @return MockObject
     */
    public function buildDispatcher(): MockObject
    {
        $dispatcher = $this->getMockBuilder(EventDispatcher::class)
                           ->disableOriginalConstructor()
                           ->getMock();
        $dispatcher->method('dispatch')
                   ->with($this->isType('string'),
                          $this->isInstanceOf(Event::class));

        return $dispatcher;
    }

    /**
     * @return MockObject
     */
    public function buildEntityManager(): MockObject
    {
        $em = $this->getMockBuilder(EntityManagerInterface::class)
                   ->disableOriginalConstructor()
                   ->getMock();

        return $em;
    }
}
