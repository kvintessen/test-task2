ARG VERSION

FROM php:${VERSION}-fpm-alpine3.18

# Install packages
RUN apk update && apk add --no-cache \
    linux-headers \
    curl \
    git \
    build-base \
    zlib-dev \
    oniguruma-dev \
    autoconf \
    bash \
    mysql-client \
    libpq-dev

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo_mysql

# Xdebug
ARG INSTALL_XDEBUG
RUN if [ ${INSTALL_XDEBUG} = true ]; \
    then \
      pecl install xdebug-3.2.0 && docker-php-ext-enable xdebug; \
    fi;

# Configure non-root user.
ARG PUID
ARG PGID
RUN apk --no-cache add shadow && \
    groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

# Source code
RUN chown www-data:www-data /var/www
COPY --chown=www-data:www-data ./ /var/www
WORKDIR /var/www

USER www-data

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
