<?php

declare(strict_types=1);

namespace Shopping\ApiTKDtoMapperBundle\Serializer;

use Exception;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Throwable;

/**
 * Class DtoEncoder.
 *
 * Registered as serializer.encoder, this DtoEncoder allows serialization of exceptions
 *
 * @package Shopping\ApiTKDtoMapperBundle\Serializer
 */
class DtoEncoder implements EncoderInterface, DecoderInterface
{
    public const FORMAT = 'dto';

    /**
     * {@inheritdoc}
     *
     * @param array<mixed> $context
     */
    public function encode($data, /* string */ $format, array $context = []): string
    {
        $exception = $context['exception'] ?? null;
        $isDebug = $context['debug'] ?? false;

        // Use simplified exception because serialization of closures inside the real exception is not allowed and crashes
        if ($exception instanceof Throwable) {
            /** @var FlattenException $data */
            $data = FlattenException::createFromThrowable($exception);

            // anonymize stack trace when not in debug mode
            if (!$isDebug) {
                $data
                    ->setFile('')
                    ->setLine(0)
                    ->setTrace([], '', null)
                    ->setPrevious(FlattenException::create(new Exception('')));
            }
        }

        return serialize($data);
    }

    /**
     * {@inheritdoc}
     *
     * @param array<mixed> $context
     */
    public function decode(/* string */ $data, /* string */ $format, array $context = [])
    {
        return unserialize($data, ['allowed_classes' => $context['allowed_classes'] ?? true]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding(/* string */ $format): bool
    {
        return $format === self::FORMAT;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDecoding(/* string */ $format): bool
    {
        return $format === self::FORMAT;
    }
}
