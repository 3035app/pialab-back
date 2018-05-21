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

export CODECEPTCMD

export TEST_SERVER_URL
export TEST_URL
export TEST_USER
export TEST_PASSWORD

if [ -n "$CODECEPTCMD" ]
then
    CODECEPTGROUP=$@
    if [ $# -eq 0 ]
    then
        CODECEPTGROUP="all" #"login" # all"
    fi

    for i in $CODECEPTGROUP
    do
        $CODECEPTCMD -g $i #--env=$CODECEPTENV
    done
fi

