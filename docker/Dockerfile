FROM php:8.1

RUN apt-get update \
    && apt-get install -y \
        git \
        wget \
        zlib1g-dev \
        libmcrypt-dev \
        libzip-dev \
        openssl \
        unzip \
        python \
        && ( \
            cd /tmp \
            && mkdir librdkafka \
            && cd librdkafka \
            && git clone https://github.com/edenhill/librdkafka.git . \
            && ./configure \
            && make \
            && make install \
        ) \
    && rm -r /var/lib/apt/lists/*

RUN docker-php-ext-install bcmath zip sockets pcntl

RUN docker-php-ext-install -j$(nproc) zip \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.1.8 \
   && chmod +x /usr/local/bin/composer \
   && composer clear-cache

WORKDIR /var/www
