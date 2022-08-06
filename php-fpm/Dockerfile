FROM php:8.0-fpm

# Install packages
RUN apt-get update && apt-get install -y \
    openssl \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libmagickwand-dev \
    inkscape

# Install PHP extensions
RUN docker-php-ext-configure \
    intl

RUN docker-php-ext-install -j$(nproc) \
    intl \
    pdo \
    pdo_mysql \
    opcache \
    zip

# Install Imagick
RUN pecl install imagick-3.6.0; \
	docker-php-ext-enable imagick; \
	rm -r /tmp/pear;

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install NodeJs
RUN apt-get install -y curl \
  && curl -sL https://deb.nodesource.com/setup_15.x | bash - \
  && apt-get install -y nodejs \
  && curl -L https://www.npmjs.com/install.sh | sh

# Get npm
RUN npm install -g npm@~7

# Install yarn
RUN npm install -g yarn

COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./timezone.ini /usr/local/etc/php/conf.d/timezone.ini
COPY ./memory_limit.ini /usr/local/etc/php/conf.d/memory_limit.ini

RUN useradd yanb -m
USER yanb

RUN git config --global user.email "20875343+yanb94@users.noreply.github.com" \
  && git config --global user.name "Yann-Elias BELLAGNECH"

WORKDIR /var/www/html