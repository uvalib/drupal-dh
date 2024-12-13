#!/bin/bash

REMOTE_HOST=dhdrupal-0.internal.lib.virginia.edu
TSTAMP=`date +%Y%m%d-%H%M%S`
BACKUP_DIR="/var/tmp"
BACKUP="${BACKUP_DIR}/dh-backup-$TSTAMP.sql"
echo "Retrieving db from $REMOTE_HOST..."
ssh -t $REMOTE_HOST sudo docker exec -it drupal-0 drush sql-dump --extra-dump=--no-tablespaces > $BACKUP || (echo "Failed: $!"; exit 1);
echo "Restoring db..."
ddev import-db --file=$BACKUP
echo "Clearing cache..."
ddev drush cr
