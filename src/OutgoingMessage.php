<?php

declare(strict_types=1);

namespace Kafkiansky\KafkaInteractor;

final class OutgoingMessage
{
    /**
     * @param array<string, string|int> $headers
     */
    public function __construct(
        public readonly string $content,
        public readonly ?int $partition = null,
        public readonly int $flags = 0,
        public readonly array $headers = [],
        public readonly ?Topic $topic = null,
    ) {
        if ('' === $this->content) {
            throw new \InvalidArgumentException('The content should not be empty.');
        }
    }
}
