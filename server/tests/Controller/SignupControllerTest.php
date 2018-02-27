<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\SignupController;
use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use function in_array;

/**
 * Class SignupControllerTest
 *
 * @package  App\Tests\Controller
 */
class SignupControllerTest extends TestCase
{
    /**
     * @var SignupController
     */
    protected $controller;

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->controller = new SignupController();
        $this->repository = $this->buildRepository();
    }

    public function testIndex(): void
    {
        $response = $this->controller->index();
        $this->assertTrue($response->isSuccessful());
    }

    public function testConfirm(): void
    {
        $response = $this->controller->confirm();
        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @dataProvider provideUsernames
     * @param string $username
     * @param bool $expected
     */
    public function testCheckUsername(string $username, bool $expected): void
    {
        if ($expected) {
            $response = $this->controller->checkUsername($username, $this->repository);
            $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        } else {
            $this->expectException(ConflictHttpException::class);
            $this->controller->checkUsername($username, $this->repository);
        }
    }

    /**
     * @dataProvider provideEmails
     * @param string $email
     * @param bool $expected
     */
    public function testCheckEmail(string $email, bool $expected): void
    {
        if ($expected) {
            $response = $this->controller->checkEmail($email, $this->repository);
            $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        } else {
            $this->expectException(ConflictHttpException::class);
            $this->controller->checkEmail($email, $this->repository);
        }
    }

    /**
     * @return array[]
     */
    public function provideUsernames(): array
    {
        return [
            ['user', false],
            ['admin', false],
            ['super', false],
            ['non_existent_username', true],
        ];
    }

    /**
     * @return array[]
     */
    public function provideEmails(): array
    {
        return [
            ['user@symfonyangularstarter', false],
            ['admin@symfonyangularstarter', false],
            ['super@symfonyangularstarter', false],
            ['non_existent_email@symfonyangularstarter', true],
        ];
    }

    /**
     * @return MockObject
     */
    protected function buildRepository(): MockObject
    {
        $mock = $this->getMockBuilder(UserRepository::class)
                     ->disableOriginalConstructor()
                     ->setMethods(['findOneByEmail', 'findOneByUsername'])
                     ->getMock();

        $mock->expects($this->any())
             ->method('findOneByEmail')
             ->with($this->isType('string'))
             ->willReturnCallback(function (string $email) {
                 $emails = [
                     'user@symfonyangularstarter',
                     'admin@symfonyangularstarter',
                     'super@symfonyangularstarter',
                 ];
                 if (in_array($email, $emails, true)) {
                     return new User();
                 }

                 return null;
             });

        $mock->expects($this->any())
             ->method('findOneByUsername')
             ->with($this->isType('string'))
             ->willReturnCallback(function (string $username) {
                 $usernames = ['user', 'admin', 'super'];
                 if (in_array($username, $usernames, true)) {
                     return new User();
                 }

                 return null;
             });

        return $mock;
    }

}
