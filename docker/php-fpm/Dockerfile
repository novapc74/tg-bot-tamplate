FROM php:8.4-fpm-alpine

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./embed/php/conf.d $PHP_INI_DIR/conf.d
COPY ./embed/php/fpm/zz-docker.conf /usr/local/etc/php/pool.d/

RUN apk update && apk add --no-cache \
        git \
        g++ \
        bash \
        fcgi \
        make \
        wget \
        unzip \
        libtool \
        icu-dev \
        autoconf \
        pkgconfig \
        libpng-dev \
        libzip-dev \
        libwebp-dev \
        freetype-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl \
    && rm -rf /var/cache/apk/*

WORKDIR /project

ARG UID=1000
RUN adduser -D -u $UID -s /bin/bash develop && \
    echo "develop:develop" | chpasswd && \
    chown -R develop:develop /project

USER develop

# Определение HEALTHCHECK
HEALTHCHECK --interval=10s --timeout=3s --retries=3 --start-period=5s \
    CMD SCRIPT_FILENAME=/ping REQUEST_METHOD=GET cgi-fcgi -bind -connect localhost:9000 || exit 1

CMD ["php-fpm"]
