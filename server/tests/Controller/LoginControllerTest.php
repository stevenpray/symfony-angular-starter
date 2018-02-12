<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LoginControllerTest
 *
 * @package App\Tests\Controller
 */
class LoginControllerTest extends WebTestCase
{

    /**
     * @dataProvider provideCredentials
     * @param null|string $username
     * @param null|string $password
     * @param bool $expected
     */
    public function testLoginCheck(?string $username, ?string $password, bool $expected): void
    {
        $params = [
            'username' => $username,
            'password' => $password,
        ];
        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/login_check', $params);
        $response = $client->getResponse();

        if ($expected) {
            $this->assertEquals(200, $response->getStatusCode());
        } else {
            $this->assertEquals(401, $response->getStatusCode());
        }
    }

    /**
     * @return array[]
     */
    public function provideCredentials(): array
    {
        return [
            ['user', 'user', true],
            ['user', null, false],
            ['admin', 'admin', true],
            ['admin', 'wrong_password', false],
            ['super', 'super', true],
            ['not_a_user', 'not_a_user', false],
        ];
    }
}
