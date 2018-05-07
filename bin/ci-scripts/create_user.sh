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

bin/console pia:user:create --email=lici@pialab.io --password=pia42
bin/console pia:user:promote lici@pialab.io --role=ROLE_SUPER_ADMIN

bin/console pia:user:create --email=api@pialab.io --password=pia

bin/console fos:oauth-server:create-client \
            --redirect-uri="http://localhost:4200" \
            --grant-type="password" \
            --grant-type="token" \
            --grant-type="refresh_token"

