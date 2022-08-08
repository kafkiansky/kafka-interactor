<?php

declare(strict_types=1);

namespace Kafkiansky\KafkaInteractor;

final class Producer
{
    public function __construct(
        private readonly \RdKafka\Producer $producer,
        private readonly Topic $topic,
    ) {
    }

    public function produce(OutgoingMessage $message, int $flushTimeout = 1000): void
    {
        $topic = $this->producer->newTopic((string)$this->topic);
        $topic->producev(
            $message->partition ?: \RD_KAFKA_PARTITION_UA,
            $message->flags,
            $message->content,
            headers: $message->headers,
        );

        $this->producer->flush($flushTimeout);
    }
}
