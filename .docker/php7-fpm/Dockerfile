ARG COMPOSER_VERSION=1.8.5

FROM composer:${COMPOSER_VERSION} as composer

# See https://github.com/docker-library/php/blob/bb16de8a711d1ba1dc76adf4665b3b1c06a06922/7.2/alpine3.9/fpm/Dockerfile
FROM php:7.2.18-fpm-alpine3.9

MAINTAINER Alexandre DEMODE <contact@alex-d.fr>

ARG TIMEZONE
ARG DOCKER_HOST_IP
ARG IDE_KEY
ARG APCU_VERSION=5.1.17

RUN apk add --no-cache \
    openssl \
    git \
    zip \
    unzip

# Add some php extensions
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
    && \
    docker-php-ext-configure \
        intl \
    && \
    docker-php-ext-install \
        intl \
        pdo_mysql \
    && \
    pecl install \
        apcu-${APCU_VERSION} \
    && \
    docker-php-ext-enable \
        apcu \
        opcache \
    && \
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)" && \
	apk add --no-cache --virtual .api-phpexts-rundeps $runDeps \
    && \
    apk del .build-deps

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative && \
	composer clear-cache

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone && \
    printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini

# Improve performances
RUN echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
#    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "realpath_cache_size=4096K" > /usr/local/etc/php/conf.d/custom.ini && \
    echo "realpath_cache_ttl=600" >> /usr/local/etc/php/conf.d/custom.ini

# Blackfire
ENV current_os=alpine
RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/$current_os/amd64/$version \
    && mkdir -p /tmp/blackfire \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
    && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz

# Install Xdebug
#RUN pecl install xdebug && docker-php-ext-enable xdebug && \
#    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "xdebug.remote_connect_back=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "xdebug.remote_host=${DOCKER_HOST_IP}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "xdebug.idekey=\"${IDE_KEY}\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "xdebug.remote_log=\"/var/www/colllect/var/logs/xdebug.log\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Xdebug profiler
#RUN echo "xdebug.profiler_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "xdebug.profiler_output_dir=\"/var/www/colllect/var/logs/xdebug_profiler\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY php-fpm.conf /usr/local/etc/

RUN mkdir -p /var/cache/colllect && \
    chown 82:82 /var/cache/colllect && \
    chmod 755 /var/cache/colllect

WORKDIR /var/www/colllect
USER www-data
