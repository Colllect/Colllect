FROM composer:2.1.14 as composer

# See https://github.com/docker-library/php/blob/master/8.0/alpine3.15/fpm/Dockerfile
FROM php:8.1.1-fpm-alpine3.15

ARG APCU_VERSION=5.1.21
ARG TIMEZONE
ARG DOCKER_HOST_IP
ARG IDE_KEY

RUN apk add --no-cache \
    make \
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

# Install wait-for
RUN curl -sL -o /usr/bin/wait-for https://raw.githubusercontent.com/eficode/wait-for/v2.2.1/wait-for \
    && chmod +x /usr/bin/wait-for

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
RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/alpine/amd64/$version \
    && mkdir -p /tmp/blackfire \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get ('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://back-blackfire:8307\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
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
#    echo "xdebug.remote_log=\"/var/www/back/var/logs/xdebug.log\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Xdebug profiler
#RUN echo "xdebug.profiler_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "xdebug.profiler_output_dir=\"/var/www/back/var/logs/xdebug_profiler\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY php-fpm.conf /usr/local/etc/

RUN apk add --no-cache shadow
RUN usermod -u 1000 www-data \
    && groupmod -g 1000 www-data

USER www-data

WORKDIR /var/www/back

COPY entrypoint.sh /opt/

ENTRYPOINT ["/opt/entrypoint.sh", "docker-php-entrypoint"]
CMD ["php-fpm"]
