FROM php:7.4-apache

WORKDIR /var/www/webroot/ROOT/

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update -y \
    && apt-get -y upgrade \
    && a2enmod rewrite \
    && a2enmod headers \
    && a2enmod mime \
    && a2enmod env \
    && a2enmod expires \
    && install-php-extensions zip \
                              gd \
                              soap \
                              pdo_mysql \
                              opcache \
                              ldap \
                              imagick \
                              pcntl \
                              mysqli \
                              mcrypt

COPY ./deployment/images/apache/conf/sis.conf /etc/apache2/conf-enabled/
COPY ./deployment/images/apache/conf/000-default.conf /etc/apache2/sites-available/
COPY ./deployment/images/php/php.ini /usr/local/etc/php/
COPY . .

RUN mkdir -p ./application/session \
    && mkdir -p ./application/cache \
    && mkdir -p ./application/logs \
    && chmod 777 -R ./application/session \
                    ./application/cache \
                    ./application/logs \
    && rm -rf ./deployment \
    && rm /usr/local/bin/install-php-extensions \
    && rm -rf /tmp/* \
    		  /var/lib/apt/lists/* \
    && truncate -s 0 /var/log/*log \
    && apt-get clean
