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

userexist=$(psql -qt --no-align -w -h ${DBHOST} -c "select count(*) from pia_user where email='lici@pialab.io';" -U ${DBUSER} -d ${DBNAME}  )

if [ $userexist -eq 0 ]
then
    bin/console pia:user:create --email=lici@pialab.io --password=pia42
    bin/console pia:user:promote lici@pialab.io --role=ROLE_SUPER_ADMIN
fi
