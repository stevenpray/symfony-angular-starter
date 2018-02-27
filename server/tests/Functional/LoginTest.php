<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LoginTest
 *
 * @package App\Tests\Functional
 */
class LoginTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var int
     */
    protected $maxLoginAttempts;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $kernel = static::bootKernel();
        $container = $kernel->getContainer();
        $this->em = $container->get('doctrine')->getEntityManager();
        $this->maxLoginAttempts = $container->getParameter('max_login_attempts');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em = null;
    }

    /**
     * @dataProvider provideCredentials
     * @param string|null $username
     * @param string|null $password
     * @param bool $expected
     */
    public function testLogin(?string $username, ?string $password, bool $expected): void
    {
        $params = [
            'username' => $username,
            'password' => $password,
        ];
        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/login', $params);
        $response = $client->getResponse();

        if ($username === null || $password === null) {
            $this->assertEquals(400, $response->getStatusCode());
        } else {
            if ($expected) {
                $this->assertEquals(200, $response->getStatusCode());
            } else {
                $this->assertEquals(401, $response->getStatusCode());
            }
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
            [null, null, false],
            [null, 'user', false],
            ['admin', 'admin', true],
            ['admin', 'wrong_password', false],
            ['super', 'super', true],
            ['not_a_user', 'not_a_user', false],
        ];
    }
}
