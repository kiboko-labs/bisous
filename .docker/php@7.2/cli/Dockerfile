FROM kiboko/php:7.2-cli-blackfire

LABEL maintainer="Grégory Planchat <gregory@kiboko.fr>"

RUN set -ex\
    && apk add \
        wget \
        autoconf \
        bash \
        binutils \
        expat \
        file \
        g++ \
        gcc \
        m4 \
        make \
        git \
        nodejs \
        npm \
    && update-ca-certificates

RUN apk add --update mysql-dev \
    && docker-php-ext-configure pdo_mysql \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure mysqli \
    && docker-php-ext-install mysqli \
    && apk del mysql-dev

RUN apk del \
        autoconf \
        bash \
        binutils \
        expat \
        file \
        g++ \
        gcc \
        gdbm \
        gmp \
        isl \
        libatomic \
        libbz2 \
        libc-dev \
        libffi \
        libgcc \
        libgomp \
        libldap \
        libltdl \
        libmagic \
        libstdc++ \
        libtool \
        m4 \
        make \
        mpc1 \
        mpfr3 \
        musl-dev \
        perl \
        pkgconf \
        pkgconfig \
        python \
        re2c \
        readline \
        sqlite-libs \
    && rm -rf /tmp/* /var/cache/apk/*

RUN pwd && curl -LSs https://box-project.github.io/box2/installer.php | php \
    && mv box.phar /usr/local/bin/box \
    && chmod 0755 /usr/local/bin/box \
    && echo "phar.readonly=0" >> /usr/local/etc/php/conf.d/phar.ini

WORKDIR /var/www/html
