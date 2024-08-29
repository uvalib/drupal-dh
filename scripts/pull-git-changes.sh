#!/bin/sh
cd /opt/drupal/util/drupal-dh
git fetch
changes=`git diff --name-only origin/main| wc -l`

if [ $changes -gt 0 ]; then
        echo $changes changes detected.
        git pull && \
	drush cim -y && \
        drush cr
fi
