FROM php:8.0-cli-alpine3.13

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER 1
ARG GROUP_ID
ARG USER_ID

COPY files/bin /usr/local/bin/
COPY --chown=www-data:www-data files/.ssh/config /home/www-data/.ssh/config

# SYS: Fix ssh permissions
RUN chmod 700 /home/www-data/.ssh && \
    chmod 400 /home/www-data/.ssh/config

# SYS: Install required packages
RUN apk --no-cache upgrade && \
    apk --no-cache add bash git sudo shadow autoconf gcc g++ make openssh

# PHP: Install php extensions
RUN mkdir /phpIni && \
    pecl channel-update pecl.php.net && \
    pecl install pcov apcu && \
    php-ext-enable pcov apcu

RUN if [ -n "$GROUP_ID" ] && [ "$GROUP_ID" -lt 60001 ]; then \
        groupmod -g ${GROUP_ID} -o www-data; \
    fi

RUN if [ -n "$USER_ID" ] && [ "$USER_ID" -lt 60001 ]; then \
        usermod -u ${USER_ID} -o www-data; \
    fi


# USER: copy home
COPY --chown=www-data:www-data files/user-home /home/www-data

# COMPOSER: install binary
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

USER www-data

WORKDIR /app
