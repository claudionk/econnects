FROM sissolucoes/sis-base:php-7.4-lts

WORKDIR /var/www/webroot/ROOT/

COPY . .

RUN mkdir -p ./application/session \
    && mkdir -p ./application/cache \
    && mkdir -p ./application/logs \
    && chmod 777 -R ./application/session \
    && chmod 777 -R ./application/cache \
    && chmod 777 -R ./application/logs \
    && rm -rf ./deployment \
    && rm -rf /tmp/* \
    && rm -rf /var/lib/apt/lists/* \
    && rm /usr/local/bin/install-php-extensions \
    && rm /usr/bin/composer \
    && truncate -s 0 /var/log/*log \
    && apt-get clean
