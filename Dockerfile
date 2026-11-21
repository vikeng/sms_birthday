FROM php:5.6-cli

# страховка: сборка упадёт, если curl вдруг отсутствует
RUN php -r 'exit(extension_loaded("curl") ? 0 : 1);'

# Свежий cacert.pem с сайта curl (нужен рабочий apt/curl на этапе сборки)
ADD https://curl.se/ca/cacert.pem /usr/local/share/cacert.pem
RUN echo 'curl.cainfo=/usr/local/share/cacert.pem' > /usr/local/etc/php/conf.d/curl-ca.ini \
    && echo 'openssl.cafile=/usr/local/share/cacert.pem' >> /usr/local/etc/php/conf.d/curl-ca.ini

WORKDIR /app
COPY script.php /app/script.php

CMD ["php", "/app/birthday.php"]
