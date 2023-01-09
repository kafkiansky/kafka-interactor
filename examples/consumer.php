<?php

declare(strict_types=1);

use Kafkiansky\KafkaInteractor\Config;
use Kafkiansky\KafkaInteractor\IncomingMessage;
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
    ->withValues([
        'auto.offset.reset' => 'earliest',
        'enable.partition.eof' => 'true',
    ])
    ->withLogLevel((string)\LOG_INFO)
;

$factory = new RdKafkaConnectionFactory($config);
$consumer = $factory->createConsumer(new Topic($topic));
$consumer->consume(function (IncomingMessage $message): void {
    var_dump($message);
}, 1000);

$consumer->consume(function (IncomingMessage $_message): void {}, onEachTick: fn (): bool => false);
