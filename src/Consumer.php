<?php

declare(strict_types=1);

namespace Kafkiansky\KafkaInteractor;

use Psr\Log\LoggerInterface;

final class Consumer
{
    public function __construct(
        private readonly \RdKafka\KafkaConsumer $consumer,
        private readonly Topic $topic,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param callable(IncomingMessage): void $consumer
     * @param (callable():bool)|null $onEachTick While callback returns true, loop will spin.
     * @param (callable(string, int):void)|null $onError
     *
     * @throws InvalidMessageReceived
     */
    public function consume(
        callable $consumer,
        int $timeout = 120 * 1000,
        ?callable $onEachTick = null,
        ?callable $onError = null,
    ): void {
        $onEachTick ??= fn(): bool => true;
        $onError ??= function(string $error, int $code): never {
            throw new InvalidMessageReceived($error, $code);
        };

        $this->consumer->subscribe([(string)$this->topic]);

        while ($onEachTick()) {
            $message = $this->consumer->consume($timeout);
            switch ($message->err) {
                case \RD_KAFKA_RESP_ERR_NO_ERROR:
                    $consumer(new IncomingMessage(
                        $message->payload,
                        $message->topic_name,
                        $message->timestamp,
                        $message->partition,
                        $message->offset,
                        $message->headers ?: [],
                    ));
                    break;
                case \RD_KAFKA_RESP_ERR__PARTITION_EOF:
                case \RD_KAFKA_RESP_ERR__TIMED_OUT:
                    $this->logger->notice('The error "{error}" from topic "{topic}" was received.', [
                        'error' => $message->errstr(),
                        'topic' => (string)$this->topic,
                    ]);
                    break;
                default:
                    $onError($message->errstr(), $message->err);
            }
        }
    }
}
