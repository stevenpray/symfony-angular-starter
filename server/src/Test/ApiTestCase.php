<?php
declare(strict_types=1);

namespace App\Test;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use function json_decode;

/**
 * Class ApiTestCase
 *
 * @package App\Test
 */
abstract class ApiTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->client->setServerParameter('ACCEPT', 'application/ld+json,application/json');
        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @param string $username
     * @param string $password
     */
    protected function authenticate(string $username, string $password = null): void
    {
        $this->client->request(Request::METHOD_POST, '/login', ['username' => $username, 'password' => $password]);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer '.$data['token']);
    }
}
