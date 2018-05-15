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


touch $HOME/.pgpass $HOME/pgpass.tmp
cp $HOME/.pgpass $HOME/pgpass.tmp
echo ${DBOAUTHHOST}:${DBOAUTHPORT}:*:${DBOAUTHROOTUSER}:${DBOAUTHROOTPASSWORD} >> $HOME/pgpass.tmp
echo ${DBOAUTHHOST}:${DBOAUTHPORT}:*:${DBOAUTHUSER}:${DBOAUTHPASSWORD} >> $HOME/pgpass.tmp
echo ${DBPIAHOST}:${DBOPIAPORT}:*:${DBPIAROOTUSER}:${DBPIAROOTPASSWORD} >> $HOME/pgpass.tmp
echo ${DBPIAHOST}:${DBOPIAPORT}:*:${DBPIAUSER}:${DBPIAPASSWORD} >> $HOME/pgpass.tmp
sort -u $HOME/pgpass.tmp > $HOME/.pgpass
chmod 600 $HOME/.pgpass
cat  $HOME/.pgpass

