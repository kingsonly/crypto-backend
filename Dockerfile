FROM php:8.2.0

# Update package lists and install required dependencies
RUN apt-get update && \
    apt-get install -y openssl zip unzip git libpng-dev zlib1g-dev libjpeg-dev && \
    docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install pdo_mysql gd

RUN docker-php-ext-install sockets

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer



# Set working directory and copy Laravel application files
WORKDIR /app
COPY . /app

# Install application dependencies

RUN composer install

# Expose the port and start the PHP server
EXPOSE $PORT
