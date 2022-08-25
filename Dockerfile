FROM php:8.1.3-apache-bullseye

ARG ENV

RUN apt-get update; \
    apt-get install -y --no-install-recommends \
    zip \
    unzip \
    git \
    vim \
    g++ \
    openssl \
    libzip-dev \
    zlib1g-dev \
    libssl-dev \
    libcurl4-openssl-dev; \
    pecl install -o -f mongodb-1.12.1; \
    echo "extension=mongodb.so" >> /usr/local/etc/php/conf.d/mongodb.ini; \
    rm -rf /tmp/pear;

RUN echo "<VirtualHost *:80> \n\
    ServerAdmin webmaster@localhost \n\
    DocumentRoot /var/app/www \n\
    ErrorLog \${APACHE_LOG_DIR}/error.log \n\
    CustomLog \${APACHE_LOG_DIR}/access.log combined \n\
    <Directory /var/app/www/> \n\
        Options Indexes FollowSymLinks \n\
        AllowOverride None \n\
        Require all granted \n\n\
        \
        RewriteEngine on \n\
        RewriteCond %{REQUEST_FILENAME} !-d \n\
        RewriteCond %{REQUEST_FILENAME} !-f \n\
        RewriteRule . index.php [L] \n\
    </Directory> \n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf;

RUN echo "[PHP]\n\
memory_limit = 2048M \n\
upload_max_filesize = 512M \n\
expose_php = Off \n\n\
date.timezone = Atlantic/Reykjavik \n" >> /usr/local/etc/php/conf.d/php.ini;

RUN if [ "$ENV" = "production" ] ; then \
    echo "opcache.enable=1\n\
opcache.jit_buffer_size=100M\n\
opcache.jit=1255\n" >> /usr/local/etc/php/conf.d/php.ini; \
fi ;

RUN if [ "$ENV" != "production" ] ; then \
    pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    echo "error_reporting = E_ALL\n\
display_startup_errors = On\n\
display_errors = On\n\
xdebug.mode = debug\n\
xdebug.start_with_request=yes\n\
xdebug.client_host=host.docker.internal\n\
xdebug.client_port=9003\n\
xdebug.idekey=myKey\n\
xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
echo 'alias cover="XDEBUG_MODE=coverage /var/app/vendor/bin/phpunit --coverage-html=\"/var/app/test/docs\""\n' >> ~/.bashrc; \
echo 'alias phpunit="/var/app/vendor/bin/phpunit"\n' >> ~/.bashrc; \
    fi ;

RUN a2enmod rewrite;

WORKDIR /var/app

# RUN chown -R www-data:www-data /var/app
# USER www-data

COPY ./composer.json ./composer.json
COPY ./composer.lock ./composer.lock

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/var/app --filename=composer --version=2.2.7

RUN if [ "$ENV" != "production" ] ; then \
    /var/app/composer install --prefer-source --no-interaction --no-cache \
    && /var/app/composer dump-autoload; \
    fi ;

RUN if [ "$ENV" = "production" ] ; then \
    /var/app/composer install --prefer-source --no-interaction --no-dev --no-cache -o \
    && /var/app/composer dump-autoload -o; \
    fi ;

COPY ./src/ ./src/
COPY ./config/ ./config/
COPY ./www/ ./www/
