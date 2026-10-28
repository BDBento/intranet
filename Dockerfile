FROM drupal:11-apache@sha256:7e5e9b7ab97aafb81800e9fd6d855d6873b1d8839d49ab77202fe1f35caa57c6

RUN a2enmod headers rewrite

# Drupal 11 support is currently available in the pinned alpha release.
# Keep this exact version until a reviewed stable release is available.
RUN composer require --no-interaction --no-progress --with-all-dependencies \
    'drupal/externalauth:2.0.15' \
    'drupal/openid_connect:3.0.0-alpha9' \
    'symfony/mime:7.4.22'

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-intranet.ini
