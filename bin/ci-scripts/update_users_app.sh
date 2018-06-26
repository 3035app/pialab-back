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


psql -h ${DBHOST} -c 'update pia_user set application_id=(select max(id) from oauth_client)' -U ${DBROOTUSER} -d ${DBNAME}
