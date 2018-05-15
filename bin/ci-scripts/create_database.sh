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


# Check if database exist before drop and re-create
#psql -w -h ${DBHOST} -U ${DBROOTUSER} -lqt | grep -w ${DBAPPUSER} | grep -w ${DBAPPNAME}
#if [ $? -ne 0 ]
#  then
#psql -w -h ${DBHOST} -c "DROP DATABASE IF EXISTS ${DBAPPNAME};" -U ${DBROOTUSER}
#psql -w -h ${DBHOST} -c "DROP ROLE IF EXISTS ${DBAPPUSER};" -U ${DBROOTUSER}
#fi

function create_database {
    # todo: remove this two line:
    #psql -w -h ${DBAPPHOST} -c "DROP DATABASE IF EXISTS ${DBAPPNAME};" -U ${DBAPPROOTUSER}
    #psql -w -h ${DBAPPHOST} -c "DROP ROLE IF EXISTS ${DBAPPUSER};" -U ${DBAPPROOTUSER}
    
    userexist=$(psql -qt -w -h ${DBAPPHOST} -U ${DBAPPROOTUSER} -c "SELECT rolname FROM pg_catalog.pg_roles WHERE rolname = '${DBAPPUSER}';"|sed -e s/' '//g)
    if [ -z ${userexist} ]
    then
        psql -w -h ${DBAPPHOST} -c "CREATE USER ${DBAPPUSER} WITH PASSWORD '${DBAPPPASSWORD}';" -U ${DBAPPROOTUSER}
    fi
    psql -w -h ${DBAPPHOST} -c "ALTER ROLE ${DBAPPUSER} WITH CREATEDB;" -U ${DBAPPROOTUSER}
    
    dbexist=$(psql -qt -w -h ${DBAPPHOST} -U ${DBAPPROOTUSER} -c "SELECT datname FROM pg_catalog.pg_database WHERE datname = '${DBAPPNAME}';"|sed -e s/' '//g)
    if [ -z ${dbexist} ]
    then
        psql -w -h ${DBAPPHOST} -c "CREATE DATABASE ${DBAPPNAME};" -U ${DBAPPROOTUSER}
    fi
    psql -w -h ${DBAPPHOST} -c "ALTER DATABASE ${DBAPPNAME} OWNER TO ${DBAPPUSER};" -U ${DBAPPROOTUSER}
    
    #psql -w -h ${DBAPPHOST} -c 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp";' -U ${DBAPPROOTUSER} -d ${DBAPPNAME}    
}


DPAPPHOST=${DBOAUTHHOST}
DPAPPROOTUSER=${DBOAUTHROOTUSER}
DBAPPNAME=${DBOAUTHNAME}
DBAPPUSER=${DBOAUTHUSER}
DBAPPPASSWORD=${DBOAUTHPASSWORD}
create_database

DPAPPHOST=${DBPIAHOST}
DPAPPROOTUSER=${DBPIAROOTUSER}
DBAPPNAME=${DBCUSTOMERNAME}
DBAPPUSER=${DBCUSTOMERUSER}
DBAPPPASSWORD=${DBCUSTOMERPASSWORD}
create_database

