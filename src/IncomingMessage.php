<?php

declare(strict_types=1);

namespace Kafkiansky\KafkaInteractor;

final class IncomingMessage
{
    /**
     * @param array<string, string|int> $headers
     */
    public function __construct(
        public readonly string $content,
        public readonly string $topic,
        public readonly int $timestamp,
        public readonly int $partition,
        public readonly int $offset,
        public readonly array $headers = [],
    ) {
    }
}
