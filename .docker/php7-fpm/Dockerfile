# See https://github.com/docker-library/php/blob/bb16de8a711d1ba1dc76adf4665b3b1c06a06922/7.3/alpine3.9/fpm/Dockerfile
FROM php:7.2.16-fpm-alpine3.9

MAINTAINER Alexandre DEMODE <contact@alex-d.fr>

ARG TIMEZONE
ARG DOCKER_HOST_IP
ARG IDE_KEY

RUN apk add --no-cache \
    openssl \
    git \
    zip \
    unzip \
    icu-dev

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini

# Add some php extensions
RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install opcache
RUN docker-php-ext-configure intl && \
    docker-php-ext-install intl
RUN docker-php-ext-install pdo pdo_mysql

# Improve performances
#RUN echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
#    echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
#    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
#    echo "realpath_cache_size=4096K" > /usr/local/etc/php/conf.d/custom.ini && \
#    echo "realpath_cache_ttl=600" >> /usr/local/etc/php/conf.d/custom.ini

# Install Xdebug
#RUN pecl install xdebug && \
#    docker-php-ext-enable xdebug && \
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

WORKDIR /var/www/colllect
USER www-data
