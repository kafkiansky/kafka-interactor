<?php

declare(strict_types=1);

namespace Kafkiansky\KafkaInteractor\Tests;

use Kafkiansky\KafkaInteractor\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testDefaultConfigEmpty(): void
    {
        self::assertCount(0, \iterator_to_array(Config::new()));
    }

    public function testPlainAuthentication(): void
    {
        $config = Config::withPlainAuthentication('test-user', 'test-password');
        self::assertEquals([
            'sasl.mechanism' => 'PLAIN',
            'security.protocol' => 'SASL_PLAINTEXT',
            'sasl.username' => 'test-user',
            'sasl.password' => 'test-password'
        ], \iterator_to_array($config));
    }

    public function testWithProperties(): void
    {
        $config = Config::withPlainAuthentication('test-user', 'test-password')
            ->withDebug()
            ->withOffsetReset('earliest')
            ->withHosts(['kafka:9093'])
            ->withGroupId('pubsub')
            ->withValues([
                'enable.partition.eof' => 'true',
            ])
        ;

        self::assertEquals([
            'sasl.mechanism' => 'PLAIN',
            'security.protocol' => 'SASL_PLAINTEXT',
            'sasl.username' => 'test-user',
            'sasl.password' => 'test-password',
            'log_level' => '7',
            'auto.offset.reset' => 'earliest',
            'metadata.broker.list' => 'kafka:9093',
            'group.id' => 'pubsub',
            'enable.partition.eof' => 'true',
        ], \iterator_to_array($config));
    }
}
