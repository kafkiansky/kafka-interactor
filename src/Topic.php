<?php

declare(strict_types=1);

namespace Kafkiansky\KafkaInteractor;

final class Topic implements \Stringable
{
    public function __construct(
        private readonly string $value,
    ) {
        if ('' === $this->value) {
            throw new \InvalidArgumentException('The topic should not be empty.');
        }
    }

    public function __toString()
    {
        return $this->value;
    }
}
