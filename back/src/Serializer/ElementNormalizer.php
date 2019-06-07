<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Model\Element\ElementInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ElementNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($element, $format = null, array $context = [])
    {
        if (!$element instanceof ElementInterface) {
            throw new InvalidArgumentException('The object must implement the "App\Model\Element\ElementInterface".');
        }

        $updated = $this->normalizer->normalize($element->getUpdated(), $format, $context);

        $fileUrl = $this->router->generate(
            'app_proxy_element',
            [
                'encodedColllectionPath' => $element->getEncodedColllectionPath(),
                'encodedElementBasename' => $element->getEncodedElementBasename(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return [
            'content' => $element->getContent(),
            'type' => $element::getType(),
            'name' => $element->getName(),
            'tags' => $element->getTags(),
            'updated' => $updated,
            'size' => $element->getSize(),
            'extension' => $element->getExtension(),
            'encodedColllectionPath' => $element->getEncodedColllectionPath(),
            'encodedElementBasename' => $element->getEncodedElementBasename(),
            'fileUrl' => $fileUrl,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ElementInterface;
    }
}
