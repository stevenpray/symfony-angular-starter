<?php
declare(strict_types=1);

namespace App\Tests\Swagger;

use App\Swagger\SwaggerDecorator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class SwaggerDecoratorTest
 *
 * @package App\Tests\Swagger
 * @covers \App\Swagger\SwaggerDecorator
 */
class SwaggerDecoratorTest extends TestCase
{
    /**
     * @var SwaggerDecorator
     */
    protected $decorator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        /**
         * @var NormalizerInterface $normalizer
         * @var Request $request
         * @var RequestStack $requestStack
         */
        $normalizer = $this->buildNormalizerInterface();
        $request = $this->buildRequest();
        $requestStack = $this->buildRequestStack($request);
        $this->decorator = new SwaggerDecorator($normalizer, $requestStack);
    }

    /**
     * @return MockObject
     */
    public function buildNormalizerInterface(): MockObject
    {
        $mock = $this->getMockBuilder(NormalizerInterface::class)
                     ->setMethods(['supportsNormalization', 'normalize'])
                     ->getMock();
        $mock->method('supportsNormalization')
             ->willReturn(true);

        return $mock;
    }

    /**
     * @return MockObject
     */
    public function buildRequest(): MockObject
    {
        $mock = $this->getMockBuilder(Request::class)
                     ->setMethods(['getHttpHost', 'getScheme'])
                     ->getMock();
        $mock->method('getHttpHost')
             ->willReturn('localhost:8001');
        $mock->method('getScheme')
             ->willReturn('http');

        return $mock;
    }

    /**
     * @param Request $request
     * @return MockObject
     */
    public function buildRequestStack(Request $request): MockObject
    {
        $mock = $this->getMockBuilder(RequestStack::class)
                     ->setMethods(['getMasterRequest'])
                     ->getMock();
        $mock->method('getMasterRequest')
             ->willReturn($request);

        return $mock;
    }

    public function testSupportsNormalization(): void
    {
        $data = null;
        $result = $this->decorator->supportsNormalization($data);
        $this->assertTrue($result);
    }

    public function testNormalize(): void
    {
        $object = null;
        $result = $this->decorator->normalize($object);
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('host', $result);
        $this->assertArrayHasKey('schemes', $result);
        $this->assertSame('localhost:8001', $result['host']);
        $this->assertCount(1, $result['schemes']);
        $this->assertContains('http', $result['schemes']);
    }
}
