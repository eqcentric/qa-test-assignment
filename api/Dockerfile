FROM prooph/composer:7.4

WORKDIR /usr/src/myapp

COPY composer.* ./
RUN composer install

COPY public public
COPY server.php ./

EXPOSE 8888

ENTRYPOINT ["php"]
CMD [ "-S", "0.0.0.0:8888", "server.php" ]
