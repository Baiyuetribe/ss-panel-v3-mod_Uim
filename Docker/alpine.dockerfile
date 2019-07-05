FROM composer:1.8.6 as composer
# Docker版问题反馈地址：https://github.com/Baiyuetribe/sspanel/pulls
FROM php:7.4.0alpha2-zts-alpine3.10
MAINTAINER azure <https://baiyue.one>
# 本镜像每月sspanel基础环境镜像
RUN apk update && apk add --no-cache libzip-dev freetype libpng libpng-dev libjpeg-turbo freetype-dev libjpeg-turbo-dev git \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd \
    --with-gd \
    --with-freetype-dir=/usr/include/ \
    --with-png-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ \
    && NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
    && docker-php-ext-install -j${NPROC} gd pdo_mysql bcmath \ 
    && rm -rf /var/cache/apk/*
COPY --from=composer /usr/bin/composer /usr/bin/composer
ENV COMPOSER_HOME /var/cache/composer
RUN mkdir /var/cache/composer 



