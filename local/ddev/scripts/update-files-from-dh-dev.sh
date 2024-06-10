#!/bin/bash -x


#calculate LOCAL_DIR
FILES_PATH=sites/default/files
LOCAL_DOC_ROOT=`ddev describe -j | jq -r '.raw.approot + "/" + .raw.docroot'`
LOCAL_DIR=${LOCAL_DOC_ROOT}/${FILES_PATH}

# Remote host details
REMOTE_USER=`whoami`
REMOTE_HOST="dh-drupal-dev-0.internal.lib.virginia.edu"
REMOTE_CONTAINER_NAME="drupal-0"
REMOTE_DIR="/opt/drupal/web/${FILES_PATH}"

echo "Syncing from $REMOTE_HOST:   $REMOTE_CONTAINER_NAME:$REMOTE_DIR"
# SSH options
SSH_OPTS="-o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null"

# Command to perform the rsync within the container
REMOTE_RSYNC_CMD="sudo docker exec -i $REMOTE_CONTAINER_NAME rsync -av $REMOTE_DIR/ -"

# Sync remote directory within the container to the local directory
ssh $SSH_OPTS $REMOTE_USER@$REMOTE_HOST "$REMOTE_RSYNC_CMD" | rsync -av -e "ssh $SSH_OPTS" - "$LOCAL_DIR/"

echo "Rsync download completed successfully."
