<?php

declare(strict_types=1);

namespace Kafkiansky\KafkaInteractor;

/**
 * @template-implements \ArrayAccess<string, string>
 * @template-implements \IteratorAggregate<string, string>
 *
 * @psalm-type ErrorCallback = callable(\RdKafka, int, string):void
 */
final class Config implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var ErrorCallback|null
     */
    private $onError;

    /**
     * @param array<string, string> $values
     * @param ErrorCallback|null $onError
     */
    private function __construct(
        private array $values,
        ?callable $onError = null,
    ) {
        $this->onError = $onError;
    }

    public static function new(): Config
    {
        return new Config([]);
    }

    /**
     * @param non-empty-string $username
     * @param non-empty-string $password
     */
    public static function withPlainAuthentication(
        string $username,
        string $password,
    ): Config {
        return new Config([
            'sasl.mechanism' => 'PLAIN',
            'security.protocol' => 'SASL_PLAINTEXT',
            'sasl.username' => $username,
            'sasl.password' => $password,
        ]);
    }

    /**
     * @param ErrorCallback $onError
     */
    public function onError(callable $onError): Config
    {
        return new Config($this->values, $onError);
    }

    /**
     * @param non-empty-string[] $hosts
     */
    public function withHosts(array $hosts): Config
    {
        return new Config(\array_merge($this->values, [
            'metadata.broker.list' => \implode(',', $hosts),
        ]));
    }

    /**
     * @param array<string, string> $values
     */
    public function withValues(array $values): Config
    {
        $config = clone $this;

        foreach ($values as $key => $value) {
            $config[$key] = $value;
        }

        return $config;
    }

    public function withDebug(): Config
    {
        return $this->withLogLevel((string) \LOG_DEBUG);
    }

    public function withLogLevel(string $level): Config
    {
        $config = clone $this;
        $config['log_level'] = $level;

        return $config;
    }

    public function withGroupId(string $groupId): Config
    {
        $config = clone $this;
        $config['group.id'] = $groupId;

        return $config;
    }

    /**
     * @psalm-param 'smallest'|'earliest'|'beginning'|'latest' $strategy
     */
    public function withOffsetReset(string $strategy): Config
    {
        $config = clone $this;
        $config['auto.offset.reset'] = $strategy;

        return $config;
    }

    public function enableAutoCommit(): Config
    {
        $config = clone $this;
        $config['enable.auto.commit'] = 'true';

        return $config;
    }

    public function disableAutoCommit(): Config
    {
        $config = clone $this;
        $config['enable.auto.commit'] = 'false';

        return $config;
    }

    /**
     * @param string $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->values[$offset]);
    }

    /**
     * @param string $offset
     */
    public function offsetGet(mixed $offset): string
    {
        return $this->values[$offset];
    }

    /**
     * @param string $offset
     * @param string $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->values[$offset] = $value;
    }

    /**
     * @param string $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->values[$offset]);
    }

    /**
     * @return \Traversable<string, string>
     */
    public function getIterator(): \Traversable
    {
        yield from $this->values;
    }

    public function toKafkaConfig(): \RdKafka\Conf
    {
        $conf = new \RdKafka\Conf();

        foreach ($this->values as $key => $value) {
            $conf->set($key, $value);
        }

        if (null !== $this->onError) {
            $conf->setErrorCb($this->onError);
        }

        return $conf;
    }
}
