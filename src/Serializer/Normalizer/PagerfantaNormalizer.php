<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Serializer\Normalizer;

use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class PagerfantaNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * @throws InvalidArgumentException when the object given is not a supported type for the normalizer
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        if (!$object instanceof PagerfantaInterface) {
            throw new InvalidArgumentException(sprintf('The object must be an instance of "%s".', PagerfantaInterface::class));
        }

        return [
            'items' => $this->normalizer->normalize($object->getIterator(), $format, $context),
            'pagination' => [
                'current_page' => $object->getCurrentPage(),
                'has_previous_page' => $object->hasPreviousPage(),
                'has_next_page' => $object->hasNextPage(),
                'per_page' => $object->getMaxPerPage(),
                'total_items' => $object->getNbResults(),
                'total_pages' => $object->getNbPages(),
            ],
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof PagerfantaInterface;
    }

    /**
     * @return array<class-string, true>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            PagerfantaInterface::class => true,
            Pagerfanta::class => true,
        ];
    }
}
