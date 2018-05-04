#!/usr/bin/env bash
set -x

# TODO share this between script (in an include)
if [ -f .env ]
then
    source .env
else
    echo "Please run this script from project root, and check .env file as it is mandatory"
    echo "If it is missing a quick solution is :"
    echo "ln -s .env.travis .env"
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

userexist=$(psql -qt -w -h ${DBHOST} -U ${DBROOTUSER} -c "SELECT rolname FROM pg_catalog.pg_roles WHERE rolname = '${DBAPPUSER}';"|sed -e s/' '//g)
if [ -z ${userexist} ]
then
    psql -w -h ${DBHOST} -c "CREATE USER ${DBAPPUSER} WITH PASSWORD '${DBAPPPASSWORD}';" -U ${DBROOTUSER}
fi
psql -w -h ${DBHOST} -c "ALTER ROLE ${DBAPPUSER} WITH CREATEDB;" -U ${DBROOTUSER}

tableexist=$(psql -qt -w -h ${DBHOST} -U ${DBROOTUSER} -c "SELECT datname FROM pg_catalog.pg_database WHERE datname = '${DBAPPNAME}';"|sed -e s/' '//g)
if [ -z ${tableexist} ]
then
    psql -w -h ${DBHOST} -c "CREATE DATABASE ${DBAPPNAME};" -U ${DBROOTUSER}
fi
psql -w -h ${DBHOST} -c "ALTER DATABASE ${DBAPPNAME} OWNER TO ${DBAPPUSER};" -U ${DBROOTUSER}

psql -w -h ${DBHOST} -c 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp";' -U ${DBROOTUSER} -d ${DBAPPNAME}
