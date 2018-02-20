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
            'emailAddress'  => 'test1@gmail.com',
            'firstname'     => 'test',
            'lastname'      => 'test',
            'plainPassword' => 'P@ssw0rd!',
        ];
        $this->client->request(Request::METHOD_POST, '/users', [], [], [], json_encode($data));
        $response = $this->client->getResponse();
        $this->assertEquals($expected, $response->isSuccessful());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);

        $url = '/users/'.$data['id'];

        $data = ['emailAddress' => 'test2@gmail.com'];
        $this->client->request(Request::METHOD_PUT, $url, [], [], [], json_encode($data));
        $response = $this->client->getResponse();
        $this->assertEquals($expected, $response->isSuccessful());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('emailAddress', $data);
        $this->assertEquals('test2@gmail.com', $data['emailAddress']);

        $this->client->request(Request::METHOD_GET, $url);
        $response = $this->client->getResponse();
        $this->assertEquals($expected, $response->isSuccessful());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);

        $this->client->request(Request::METHOD_DELETE, '/users/'.$data['id']);
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
