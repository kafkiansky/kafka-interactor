<?php

declare(strict_types=1);

namespace Kafkiansky\KafkaInteractor;

interface ConnectionFactory
{
    public function createProducer(Topic $topic): Producer;
    public function createConsumer(Topic $topic): Consumer;
}
