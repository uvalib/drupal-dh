#
# build the target container
#
FROM public.ecr.aws/docker/library/drupal:9.5.11

# update the packages
RUN apt-get -y update && apt-get -y upgrade && apt-get -y install default-mysql-client git less vim

# set the timezone appropriatly
ENV TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Specify home
ENV APP_HOME /drupal-dh
WORKDIR $APP_HOME

# Move in necessary assets
COPY package/data/container_bash_profile /root/.profile

# install other dependancies
RUN pecl install apcu-5.1.22 && docker-php-ext-enable apcu

# Add the build tag
ARG BUILD_TAG
RUN test -n "$BUILD_TAG" && touch $APP_HOME/buildtag.build-$BUILD_TAG || touch $APP_HOME/buildtag.build-0

#
# end of file
#
