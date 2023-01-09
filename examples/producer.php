<?php

declare(strict_types=1);

use Kafkiansky\KafkaInteractor\Config;
use Kafkiansky\KafkaInteractor\OutgoingMessage;
use Kafkiansky\KafkaInteractor\RdKafkaConnectionFactory;
use Kafkiansky\KafkaInteractor\Topic;

require_once __DIR__.'/../vendor/autoload.php';

$host = \getenv('KAFKA_HOST');
$username = \getenv('KAFKA_USERNAME');
$password = \getenv('KAFKA_PASSWORD');
$groupId = \getenv('KAFKA_GROUP_ID');
$topic = \getenv('KAFKA_TOPIC');

$config = Config::withPlainAuthentication($username, $password)
    ->withGroupId($groupId)
    ->withHosts([$host])
    ->withOffsetReset('earliest')
    ->withValues([
        'enable.partition.eof' => 'true',
    ])
    ->withLogLevel((string)\LOG_INFO)
    ->onError(function (): void {
        // log or throw exception
    });
;

$factory = new RdKafkaConnectionFactory($config);
$producer = $factory->createProducer(new Topic($topic));
$producer->produce(new OutgoingMessage(content: 'Hello, world.', headers: ['x-php-version' => '8.1']));

