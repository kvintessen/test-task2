
FROM nginx:alpine

RUN rm /etc/nginx/conf.d/default.conf

RUN apk update && apk upgrade && apk add --no-cache bash

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

# Set upstream conf and remove the default conf
ARG PREFIX
ARG PHP_UPSTREAM_CONTAINER
ARG PHP_UPSTREAM_PORT
RUN echo "upstream php-upstream { server ${PREFIX}-${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf