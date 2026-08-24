FROM php:8.2-cli

WORKDIR /usr/src/app

COPY . .

EXPOSE 10000

CMD php -S 0.0.0.0:10000
