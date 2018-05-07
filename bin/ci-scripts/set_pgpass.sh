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


if [ -n "${DBHOST}" ]
then
    touch $HOME/.pgpass $HOME/pgpass.tmp
    cp $HOME/.pgpass $HOME/pgpass.tmp
    echo ${DBHOST}:5432:*:${DBROOTUSER}:${DBROOTPASSWORD} >> $HOME/pgpass.tmp
    echo ${DBHOST}:5432:*:${DBAPPUSER}:${DBAPPPASSWORD} >> $HOME/pgpass.tmp
    sort -u $HOME/pgpass.tmp > $HOME/.pgpass
    chmod 600 $HOME/.pgpass
    cat  $HOME/.pgpass
fi
