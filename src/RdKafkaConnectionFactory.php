<?php

declare(strict_types=1);

namespace Kafkiansky\KafkaInteractor;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class RdKafkaConnectionFactory implements ConnectionFactory
{
    public function __construct(
        private readonly Config $config,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function createConsumer(Topic $topic): Consumer
    {
        return new Consumer(
            new \RdKafka\KafkaConsumer($this->config->toKafkaConfig()),
            $topic,
            $this->logger,
        );
    }

    public function createProducer(Topic $topic): Producer
    {
        return new Producer(
            new \RdKafka\Producer($this->config->toKafkaConfig()),
            $topic,
        );
    }
}
