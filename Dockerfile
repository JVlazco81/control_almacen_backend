FROM php:8.3-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    sudo \
    && docker-php-ext-install pdo_mysql zip

# Configurar usuario y permisos
ARG USER_ID=1000
ARG GROUP_ID=1000

RUN groupadd -g ${GROUP_ID} laravel && \
    useradd -u ${USER_ID} -g laravel -m laravel && \
    usermod -aG sudo laravel && \
    echo "laravel ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers && \
    mkdir -p /var/www/storage /var/www/bootstrap/cache && \
    chown -R laravel:laravel /var/www

# Instalar Composer como root
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configurar entorno de trabajo
USER laravel
WORKDIR /var/www