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

if [ -z "${CLIENTURL}" ]
then
    CLIENTURL="http://localhost:4200"
fi

bin/console pia:application:create \
            --name="Default App" \
            --url="${CLIENTURL}" \


# we do not want to parse the output of the fos:oauth-server command
lid=$(psql -qt --no-align -w -h ${DBHOST} -c 'select max(id) from oauth_client;' -U ${DBUSER} -d ${DBNAME}  )
clientid=$(psql -qt --no-align -w -h ${DBHOST} -c "select id||'_'||random_id from oauth_client where id=$lid;" -U ${DBUSER} -d ${DBNAME}  )
clientsecret=$(psql -qt --no-align -w -h ${DBHOST} -c "select secret from oauth_client where id=$lid;" -U ${DBUSER} -d ${DBNAME}  )

echo "APICLIENTID=$clientid" > .api.env
echo "APICLIENTSECRET=$clientsecret" >> .api.env
