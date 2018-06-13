#!/usr/bin/env bash
set -ex

if [ -z ${Name} ]
then
    Name=Pialab-back
fi

if [ -z ${Branch} ]
then
    Branch=$(git name-rev  --name-only $(git rev-parse HEAD) | sed -e s/\\^.*//g | awk -F'/' '{print $(NF)}')
fi

# Clean current git dir
git clean -df
git checkout -- .

# optimize loader and remove dev package from vendor
composer install --no-dev --no-scripts --no-interaction
composer dump-autoload --optimize --no-interaction
# --optimize-autoloader


Filename=${Name}_${Branch}.tar.gz

rm -f ${Filename}

rm -rf \
   var/logs/* \
   var/cache/* \
   *.log \
   *.nbr \
   *.dist

tar --exclude-vcs \
    --exclude=build \
    --exclude=bin/git-scripts \
    -czhf ${Filename} ./* .env

sha256sum ${Filename} > ${Filename}.sha256.txt
