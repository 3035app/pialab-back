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

if [ -z "${DBHOST}" ]
then
    echo "Please add DBHOST in .env file as it is mandatory"
    exit 42
fi

# Check if database exist before drop and re-create
#psql -w -h ${DBHOST} -U ${DBROOTUSER} -lqt | grep -w ${DBAPPUSER} | grep -w ${DBAPPNAME}
#if [ $? -ne 0 ]
#  then
#psql -w -h ${DBHOST} -c "DROP DATABASE IF EXISTS ${DBAPPNAME};" -U ${DBROOTUSER}
#psql -w -h ${DBHOST} -c "DROP ROLE IF EXISTS ${DBAPPUSER};" -U ${DBROOTUSER}
#fi

    # todo: remove this two line:
    #psql -w -h ${DBHOST} -c "DROP DATABASE IF EXISTS ${DBAPPNAME};" -U ${DBROOTUSER}
    #psql -w -h ${DBHOST} -c "DROP ROLE IF EXISTS ${DBAPPUSER};" -U ${DBROOTUSER}
    
    userexist=$(psql -qt -w -h ${DBHOST} -U ${DBROOTUSER} -c "SELECT rolname FROM pg_catalog.pg_roles WHERE rolname = '${DBUSER}';"|sed -e s/' '//g)
    if [ -z ${userexist} ]
    then
        psql -w -h ${DBHOST} -c "CREATE USER ${DBUSER} WITH PASSWORD '${DBPASSWORD}';" -U ${DBROOTUSER}
    fi
    psql -w -h ${DBHOST} -c "ALTER ROLE ${DBUSER} WITH CREATEDB;" -U ${DBROOTUSER}
    
    dbexist=$(psql -qt -w -h ${DBHOST} -U ${DBROOTUSER} -c "SELECT datname FROM pg_catalog.pg_database WHERE datname = '${DBNAME}';"|sed -e s/' '//g)
    if [ -z ${dbexist} ]
    then
        psql -w -h ${DBHOST} -c "CREATE DATABASE ${DBNAME};" -U ${DBROOTUSER}
    fi
    psql -w -h ${DBHOST} -c "ALTER DATABASE ${DBNAME} OWNER TO ${DBUSER};" -U ${DBROOTUSER}
    
    #psql -w -h ${DBHOST} -c 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp";' -U ${DBROOTUSER} -d ${DBAPPNAME}    
