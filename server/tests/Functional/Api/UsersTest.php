<?php
declare(strict_types=1);

namespace App\Tests\Functional\Api;

use App\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use function json_decode;
use function json_encode;

/**
 * Class UsersTest
 *
 * @package App\Tests\Functional\Api
 */
class UsersTest extends ApiTestCase
{
    /**
     * @dataProvider provideCredentials
     * @param string $username
     * @param string $password
     * @param bool $expected
     */
    public function testPostPutGetDelete(string $username, string $password, bool $expected): void
    {
        $this->authenticate($username, $password);
        $data = [
            'username'      => 'test',
            'email'  => 'test1@gmail.com',
            'firstname'     => 'test',
            'lastname'      => 'test',
            'plainPassword' => 'P@ssw0rd',
        ];
        $this->client->request(Request::METHOD_POST, '/api/users', [], [], [], json_encode($data));
        $response = $this->client->getResponse();
        $this->assertEquals($expected, $response->isSuccessful());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);

        $url = '/api/users/'.$data['id'];

        $data = ['email' => 'test2@gmail.com'];
        $this->client->request(Request::METHOD_PUT, $url, [], [], [], json_encode($data));
        $response = $this->client->getResponse();
        $this->assertEquals($expected, $response->isSuccessful());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('email', $data);
        $this->assertEquals('test2@gmail.com', $data['email']);

        $this->client->request(Request::METHOD_GET, $url);
        $response = $this->client->getResponse();
        $this->assertEquals($expected, $response->isSuccessful());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);

        $this->client->request(Request::METHOD_DELETE, '/api/users/'.$data['id']);
        $response = $this->client->getResponse();
        $this->assertEquals($expected, $response->isSuccessful());
    }

    /**
     * @return array[]
     */
    public function provideCredentials(): array
    {
        return [
            ['admin', 'admin', true],
            ['super', 'super', true],
        ];
    }
}
