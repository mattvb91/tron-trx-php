FROM php:7.2-alpine

RUN apk --no-cache add libmcrypt-dev \
        libltdl \
        icu-dev \
        freetype-dev \
        m4 \
        perl \
        autoconf \
        libc6-compat \
        build-base \
        curl \
        git \
        openssh \
        openssl \
        tini \
        bash \
        gmp \
        gmp-dev \
    && docker-php-ext-install mbstring \
        bcmath \
        opcache \
        gmp \
	&& apk del icu-dev \
        freetype-dev \
        build-base \
        gmp-dev \
        autoconf \
        make \
	    perl

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
ENV COMPOSER_VERSION 1.7.3

ARG HOST_USER_UID=1000
ARG HOST_USER_GID=1000

COPY entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh \
 && echo "memory_limit=-1" > "$PHP_INI_DIR/conf.d/memory-limit.ini" \
 && echo "date.timezone=${PHP_TIMEZONE:-UTC}" > "$PHP_INI_DIR/conf.d/date_timezone.ini" \
 && php -r " \
    copy('https://getcomposer.org/installer', 'composer-setup.php'); \
    if (hash_file('SHA384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') \
    { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
 && php composer-setup.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION} \
 && rm composer-setup.php \
 && composer --ansi --version --no-interaction \
 && rm -rf /tmp/* /tmp/.htaccess \
 && addgroup -g $HOST_USER_GID developers \
 && adduser -D -u $HOST_USER_UID -G developers developer

WORKDIR /app

ENTRYPOINT ["/entrypoint.sh"]

CMD ["composer"]
