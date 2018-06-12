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

CLIENT_ID=$(openssl rand -hex 24)
CLIENT_SECRET=$(openssl rand -hex 24)

bin/console pia:application:create \
            --name="Default App" \
            --url="${CLIENTURL}" \
            --client-id="${CLIENT_ID}" \
            --client-secret="${CLIENT_SECRET}"

echo "APICLIENTID=${CLIENT_ID}" > .api.env
echo "APICLIENTSECRET=${CLIENT_SECRET}" >> .api.env
