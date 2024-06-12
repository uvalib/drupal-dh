#
# build the target container
#
FROM public.ecr.aws/docker/library/drupal:10.2

# update the packages
RUN apt-get -y update && \
	apt-get -y upgrade && \
	apt-get -y install git less vim && \
	apt-get -y install default-mysql-client sqlite3

# set the timezone appropriatly
ENV TZ=America/New_York
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Specify home
ENV APP_HOME /drupal-dh
WORKDIR $APP_HOME

# Move in necessary assets
COPY package/data/container_bash_profile /root/.profile
# COPY package/data/files/opt/drupal/composer.json /opt/drupal/composer.json
# COPY package/data/files/opt/drupal/composer.lock /opt/drupal/composer.lock

# github checkout drupal-dh dir and symlink
RUN git clone https://github.com/uvalib/drupal-dh util/drupal-dh \
	&& ln -sf util/drupal-dh/package/data/files/opt/drupal/simplesaml /opt/drupal/simplesaml \
	&& ln -sf util/drupal-dh/package/data/files/opt/drupal/composer.lock /opt/drupal/composer.lock \
	&& ln -sf util/drupal-dh/package/data/files/opt/drupal/composer.json /opt/drupal/composer.json \
	&& ln -sf util/drupal-dh/package/data/files/opt/drupal/config /opt/drupal/config \
	&& ln -sf util/drupal-dh/package/data/files/opt/drupal/simplesaml /opt/drupal/simplesaml \
	&& ls -l /opt/drupal

# install other dependancies
RUN pecl install apcu-5.1.22 && docker-php-ext-enable apcu

# need to mount volume in /opt/drupal/web/sites (is that done here?)
# need to run composer install here
# Note: themes will be deployed via composer
# Need to flush and rebuild caches here

WORKDIR /opt/drupal
RUN ls -l
RUN composer install
RUN mkdir /var/run/sqlite && chown www-data:www-data /var/run/sqlite && \
	mkdir -p /var/cache/simplesamlphp && chown www-data:www-data /var/cache/simplesamlphp

WORKDIR $APP_HOME
# Add the build tag
ARG BUILD_TAG
RUN test -n "$BUILD_TAG" && touch $APP_HOME/buildtag.build-$BUILD_TAG || touch $APP_HOME/buildtag.build-0

#
# end of file

