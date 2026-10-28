FROM drupal:11-apache

RUN a2enmod headers rewrite

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-intranet.ini
