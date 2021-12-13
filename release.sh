#!/usr/bin/env bash

APP_COMPOSER_INSTALL="yes";
APP_TEST="no";

# getting versions
while getopts ":c:t:" opt
   do
     case $opt in
        c ) APP_COMPOSER_INSTALL=$OPTARG;;
        t ) APP_TEST=$OPTARG;;
     esac
done



# preparing git to checkout
git stash

# getting the last tag and checking it out
git pull --tags
latestTag=$(git describe --tags `git rev-list --tags --max-count=1`)
git checkout tags/$latestTag


# running composer install (if requested)
if [ ${APP_COMPOSER_INSTALL} = "yes" ]
then
    echo "running composer install"
    php7.4 /usr/local/bin/composer install --prefer-dist --no-dev
#    composer install --prefer-dist --no-dev
fi

# running composer install (if requested)
rm -f web/app_dev.php
rm -f web/back_dev.php
if [ ${APP_TEST} = "no" ]
then
    echo "# removing the app_dev file"
    rm -f web/app_sandbox.php
    rm -f web/back_sandbox.php
fi


# clear cache
# TODO!

git describe