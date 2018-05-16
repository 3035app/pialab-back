#!/usr/bin/env bash
set -ex

# TODO share this between script (in an include)
if [ -f .env ]
then
    source .env
else
    echo "Please run this script from project root, and check .env file as it is mandatory"
    echo "If it is missing a quick solution is :"
    echo "ln -s .env.dist .env"
    exit 42
fi

bin/console doctrine:database:create --if-not-exists --no-interaction 

bin/console doctrine:migration:migrate --no-interaction

bin/console doctrine:schema:validate --no-interaction 
