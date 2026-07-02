FROM nginx/unit:1.29.0-php8.1

RUN apt-get -y update
RUN apt-get -y install git
RUN apt-get install zip unzip
RUN apt-get install -y supervisor


# Install Extension PHP Easy Installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions
#install php extensions
RUN install-php-extensions \
    bcmath \
    pdo \
    pdo_mysql \
    intl \
    gd \
    gmp \
    zip

#install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

ADD --chown=unit:unit composer.lock composer.json /app/
RUN composer install --no-plugins --no-scripts && \
    composer clear-cache

ADD --chown=unit:unit ./api /app/api
ADD --chown=unit:unit ./common /app/common
ADD --chown=unit:unit ./console /app/console
ADD --chown=unit:unit ./database /app/database
ADD --chown=unit:unit ./storage /app/storage

COPY --chown=unit:unit ./docker /app/docker
COPY --chown=unit:unit ./.env.dist /app/.env
COPY --chown=unit:unit ./.env.dist /app/.env.dist

COPY --chown=unit:unit .unit.conf.json /docker-entrypoint.d/.unit.conf.json
COPY --chown=unit:unit .unit.conf.json app/.unit.conf.json
CMD ["unitd", "--no-daemon", "--control", "127.0.0.1:8881"]
EXPOSE 80
