Resources for running this project locally.
the 'ddev' directory is a DDEV project

Try: 
	cd ./ddev
	ddev start
	ddev composer install
	cd ./scripts
	./update-db-from-dev.sh
	./update-files-from-dev.sh
	ddev drush cr
	ddev launch
