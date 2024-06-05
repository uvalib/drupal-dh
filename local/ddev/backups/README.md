Backups are created using something like this:

docker exec -it drupal-0 drush sql-dump --extra-dump=--no-tablespaces --result-file=/tmp/dh-backup-`date +%f-%T`.sql

(NB: This particular invocation creates a dump INSIDE the container. A more practical solution or process should be developed).
