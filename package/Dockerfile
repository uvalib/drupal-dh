#
# build the target container
#
FROM public.ecr.aws/docker/library/drupal:10.2

# update the packages
RUN apt-get -y update && \
	apt-get -y upgrade && \
	apt-get -y git less vim \
	apt-get -y install default-mysql-client sqlite3

# set the timezone appropriatly
ENV TZ=America/New_York
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Specify home
ENV APP_HOME /drupal-dh
WORKDIR $APP_HOME

# Move in necessary assets
COPY package/data/container_bash_profile /root/.profile
COPY package/data/files/opt/drupal/composer.json /opt/drupal/composer.json
COPY package/data/files/opt/drupal/composer.lock /opt/drupal/composer.lock

# install other dependancies
RUN pecl install apcu-5.1.22 && docker-php-ext-enable apcu

# Add the build tag
ARG BUILD_TAG
RUN test -n "$BUILD_TAG" && touch $APP_HOME/buildtag.build-$BUILD_TAG || touch $APP_HOME/buildtag.build-0

#
# end of file

