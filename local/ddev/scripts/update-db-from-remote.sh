#!/bin/bash

# Configuration
HOSTS="prod:dhdrupal-0.internal.lib.virginia.edu dev:dh-drupal-dev-0.internal.lib.virginia.edu"
DEFAULT_ENV="dev"
SCRIPT_DIR=$(dirname "$(readlink -f "$0")")
BACKUP_DIR="$SCRIPT_DIR/../backups"
IMPORT_DB=true
USE_LATEST=false

while getopts "hnl" opt; do
    case $opt in
        h) show_help=true ;;
        n) IMPORT_DB=false ;;
        l) USE_LATEST=true ;;
        *) show_help=true ;;
    esac
done
shift $((OPTIND-1))

if [[ ! -d "$BACKUP_DIR" ]]; then
    echo "Creating backup directory: $BACKUP_DIR"
    mkdir -p "$BACKUP_DIR"
fi

show_help() {
    echo "Usage: $0 [-n] [-l] [env]"
    echo "Options:"
    echo "  -n    Download only, skip import and cache clear"
    echo "  -l    Use latest backup instead of downloading new one"
    echo "  -h    Show this help message"
    echo "Environments:"
    for pair in $HOSTS; do
        env=${pair%%:*}
        host=${pair#*:}
        [[ "$env" == "$DEFAULT_ENV" ]] && default=" (default)" || default=""
        echo "  $env: $host$default"
    done
    exit 1
}

[[ -n "$show_help" ]] && show_help
ENV="${1:-$DEFAULT_ENV}"

HOST=""
for pair in $HOSTS; do
    if [[ "${pair%%:*}" == "$ENV" ]]; then
        HOST="${pair#*:}"
        break
    fi
done

[[ -z "$HOST" ]] && echo "Error: Unknown environment '$ENV'" && show_help

if $USE_LATEST; then
    # Find latest backup for specified environment
    LATEST_BACKUP=$(ls -t "${BACKUP_DIR}/dh-backup-${ENV}-"*.sql.gz 2>/dev/null | head -n1)
    if [[ -z "$LATEST_BACKUP" ]]; then
        echo "Error: No existing backup found for environment '$ENV'"
        exit 1
    fi
    
    # Check backup age
    BACKUP_AGE=$(( ($(date +%s) - $(date -r "$LATEST_BACKUP" +%s)) / 3600 ))
    echo "Using latest backup: $LATEST_BACKUP"
    echo "Warning: Backup is $BACKUP_AGE hours old"
    BACKUP=$LATEST_BACKUP
else
    eval "$(ssh-agent -s)"
    ssh-add

    echo "Using host: $HOST"

    echo "Checking connection to $HOST..."
    if [[ $(ssh -o ConnectTimeout=5 "$HOST" sudo true) ]]; then
        echo "Connection to $HOST failed.  Check that you are on VPN"
        exit 1
    fi     

    TSTAMP=$(date +%Y%m%d-%H%M%S)
    BACKUP="${BACKUP_DIR}/dh-backup-$ENV-$TSTAMP.sql.gz"
    TEMP_SQL=$(mktemp)

    echo "Retrieving db from $HOST..."
    ssh -t "$HOST" sudo docker exec -it drupal-0 drush sql-dump --extra-dump=--no-tablespaces | tee "$TEMP_SQL" | gzip > "$BACKUP"

    echo "Validating SQL dump..."
    if ! ddev mysql < "$TEMP_SQL" --silent --execute="quit" 2>/dev/null; then
        echo "Error: Invalid SQL syntax detected in dump"
        rm "$BACKUP" "$TEMP_SQL"
        exit 1
    fi

    if [[ $(file $BACKUP) == *truncated* ]]; then
        echo "Error: truncated SQL detected in dump.  Check to see if connection timed out.  NB: this script assumes VPN access to the host."
        rm "$BACKUP" "$TEMP_SQL"
        exit 1
    fi

    rm "$TEMP_SQL"

    if [[ ! -f "$BACKUP" ]] || [[ ! -s "$BACKUP" ]]; then
        echo "Error: Backup file is empty or not created"
        rm -f "$BACKUP"
        exit 1
    fi

    if ! gzip -t "$BACKUP" 2>/dev/null; then
        echo "Error: Backup file is not a valid gzip file"
        rm "$BACKUP"
        exit 1
    fi

    echo "Backup created successfully: $BACKUP ($(du -h "$BACKUP" | cut -f1))"
fi

if $IMPORT_DB; then
    echo "Restoring db..."
    gunzip -c "$BACKUP" | ddev import-db
    echo "Clearing cache..."
    ddev drush cr
fi
