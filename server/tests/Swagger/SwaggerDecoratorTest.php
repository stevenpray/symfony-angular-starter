<?php
declare(strict_types=1);

namespace App\Tests\Swagger;

use App\Swagger\SwaggerDecorator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class SwaggerDecoratorTest
 *
 * @package App\Tests\Swagger
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
        $decorated = $this->getMockBuilder(NormalizerInterface::class)
                          ->setMethods(['supportsNormalization', 'normalize'])
                          ->getMock();
        $decorated->method('supportsNormalization')->willReturn(true);

        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(['getHttpHost', 'getScheme'])
                        ->getMock();
        $request->method('getHttpHost')->willReturn('localhost:8001');
        $request->method('getScheme')->willReturn('http');

        $requestStack = $this->getMockBuilder(RequestStack::class)
                             ->setMethods(['getMasterRequest'])
                             ->getMock();
        $requestStack->method('getMasterRequest')->willReturn($request);

        /**
         * @var NormalizerInterface $decorated
         * @var RequestStack $requestStack
         */
        $this->decorator = new SwaggerDecorator($decorated, $requestStack);
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
