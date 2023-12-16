#!/bin/bash

API_GIT_REPOSITORY='https://github.com/joaomateusferr/financial-dashboard-api.git'

APACHE_SITE_PATH='/var/www/html'
DEFAULT_APACHE_SITES_PATH='/etc/apache2/sites-available/000-default.conf'
DEFAULT_APACHE_CONF_PATH='/etc/apache2/apache2.conf'

REPO_FOLDER_NAME=$(basename -s .git $API_GIT_REPOSITORY)
REPO_FOLDER="$APACHE_SITE_PATH/$REPO_FOLDER_NAME"

git -C $APACHE_SITE_PATH/ clone $API_GIT_REPOSITORY
chmod -R 777 $APACHE_SITE_PATH/

sed -i "s|$APACHE_SITE_PATH|$REPO_FOLDER/public|g" $DEFAULT_APACHE_SITES_PATH
sed -i "s|AllowOverride None|AllowOverride All|g" $DEFAULT_APACHE_CONF_PATH

DEPENDENCY_GIT_REPOSITORY='https://github.com/joaomateusferr/dividend-map.git'
git -C $APACHE_SITE_PATH/ clone $DEPENDENCY_GIT_REPOSITORY