<?php
declare(strict_types=1);

namespace App\Swagger;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class SwaggerDecorator
 *
 * @package App\Swagger
 */
class SwaggerDecorator implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $decorated;

    /**
     * @var Request
     */
    protected $request;

    /**
     * SwaggerDecorator constructor.
     *
     * @param NormalizerInterface $decorated
     * @param RequestStack $request
     */
    public function __construct(NormalizerInterface $decorated, RequestStack $request)
    {
        $this->decorated = $decorated;
        $this->request = $request->getMasterRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);
        $docs['schemes'] = [$this->request->getScheme()];
        $docs['host'] = $this->request->getHttpHost();

        return $docs;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
