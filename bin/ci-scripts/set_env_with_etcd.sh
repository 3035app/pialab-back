#!/usr/bin/env bash
set -ex

export ETCDCTL_API=3

# rand number to avoid build colision (same db used by two build)
if [ ! -f shuf.nbr ]
then
    shuf -i 200-600 -n 1 > shuf.nbr
fi

if [ -z "$Suffix" ]
   then
       #RND may contain branch with '-' or upper case char which may not work as database name for postgre
       Suffix=$(echo $RND|sed -e s/-/_/g|tr '[:upper:]' '[:lower:]')$(echo -n $(cat shuf.nbr ))
fi

Suffix=$(echo $Suffix | sed s/'\W'//g | tr '[:upper:]' '[:lower:]')

if [ -z "$Prefix" ]
then
    Prefix="/pialabback/build/$Suffix"
fi

if [ -z "$ETCDHOST" ]
then
    ETCDHOST="etcd.host"
fi
ETCDENDPOINT="--endpoints=http://${ETCDHOST}:2379"

if [ -z "$ETCDCTLCMD" ]
then
    ETCDCTLCMD="etcdctl"
fi

if [ -z "${BUILDENV}" ]
then
    BUILDENV="dev"
fi

if [ -z "$SYMFONYENV" ]
then
    SYMFONYENV=${BUILDENV}
fi

if [ -z "$SYMFONYCORSALLOW" ]
then
    SYMFONYCORSALLOW='#^https?://localhost:?[0-9]*$'
    if [ -n "${CLIENTURL}" ]
    then
        CLIENTURLREGEX=$(echo $CLIENTURL | sed -e s:.*//::g | sed -e s:/.*::g | sed -e s/:.*//g | sed -e s/'\.'/'\\.'/g)
        SYMFONYCORSALLOW='^https?://'$CLIENTURLREGEX':?[0-9]*$'
    fi
fi

if [ -z "$DatabaseName" ]
then
    DatabaseName=pia_db_$Suffix
fi

if [ -z "$DatabaseUser" ]
then
    DatabaseUser=pia_user_$Suffix
fi

if [ -z "$DatabasePassword" ]
then
    DatabasePassword=$(openssl rand -hex 8)
fi

if [ -z "$MailerUrl" ]
then
    MailerUrl=smtp://127.0.0.1:1025

fi

if [ -z "${CLIENTURL}" ]
then
    CLIENTURL="http://localhost:4200"
fi

CLIENT_ID=$(openssl rand -hex 24)
if [ -n "$ClientId" ]
then
    CLIENT_ID=$ClientId
fi

CLIENT_SECRET=$(openssl rand -hex 24)
if [ -n "$ClientSecret" ]
then
    CLIENT_SECRET=$ClientSecret
fi




# get postgres default
postgreshost=$($ETCDCTLCMD get /default/postgres/hostname --print-value-only $ETCDENDPOINT)
postgresuser=$($ETCDCTLCMD get /default/postgres/root/username --print-value-only $ETCDENDPOINT)
postgrespass=$($ETCDCTLCMD get /default/postgres/root/password --print-value-only $ETCDENDPOINT)


# usealready setted variable If They exist
if [ -n "${DatabaseHost}" ]
then
    postgreshost=${DatabaseHost}
fi

if [ -n "${DatabaseRootUser}" ]
then
    postgresuser=${DatabaseRootUser}
fi

if [ -n "${DatabaseRootPassword}" ]
then
    postgrespass=${DatabaseRootPassword}
fi


# get selenium default
SeleniumHost=$($ETCDCTLCMD get /default/selenium/hostname --print-value-only $ETCDENDPOINT)
$ETCDCTLCMD put $Prefix/selenium/hostname $SeleniumHost $ETCDENDPOINT

# set postgres env
$ETCDCTLCMD put $Prefix/postgres/hostname $postgreshost $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/postgres/root/username $postgresuser $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/postgres/root/password $postgrespass $ETCDENDPOINT

$ETCDCTLCMD put $Prefix/postgres/default/dbname $DatabaseName $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/postgres/default/username $DatabaseUser $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/postgres/default/password $DatabasePassword $ETCDENDPOINT

$ETCDCTLCMD put $Prefix/smtp/default/url $MailerUrl $ETCDENDPOINT

# set symfony env
$ETCDCTLCMD put $Prefix/symfony/env $SYMFONYENV $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/symfony/cors/allow $SYMFONYCORSALLOW $ETCDENDPOINT

$ETCDCTLCMD put $Prefix/client/url $CLIENTURL $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/client/id $CLIENT_ID $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/client/secret $CLIENT_SECRET $ETCDENDPOINT

# get ip
currentip=$(hostname -i) # works only if the host name can be resolved
$ETCDCTLCMD put $Prefix/url/addr $currentip':8000' $ETCDENDPOINT

$ETCDCTLCMD get --prefix $Prefix $ETCDENDPOINT

confd -onetime -backend etcdv3 -node http://${ETCDHOST}:2379 -confdir ./etc/confd -log-level debug -prefix $Prefix
