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

if [ -z "$Prefix" ]
then
    Prefix="/pia/build/$suffix"
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

if [ -z "$SYMFONYENV" ]
then
    SYMFONYENV="dev"
fi

# check
$ETCDCTLCMD get  --Prefix '/default' $ETCDENDPOINT

# get postgres default
postgreshost=$($ETCDCTLCMD get /default/postgres/hostname --print-value-only $ETCDENDPOINT)
postgresuser=$($ETCDCTLCMD get /default/postgres/root/username --print-value-only $ETCDENDPOINT)
postgrespass=$($ETCDCTLCMD get /default/postgres/root/password --print-value-only $ETCDENDPOINT)
# TODO add a check default cnx with psql

# set postgres env
$ETCDCTLCMD put $Prefix/postgres/hostname $postgreshost $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/postgres/root/username $postgresuser $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/postgres/root/password $postgrespass $ETCDENDPOINT

$ETCDCTLCMD put $Prefix/postgres/oauth/dbname pia_oauth_db_$Suffix $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/postgres/oauth/username pia_oauth_user_$Suffix $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/postgres/oauth/password pia_oauth_pass_$Suffix $ETCDENDPOINT

$ETCDCTLCMD put $Prefix/postgres/customer/dbname pia_cust_db_$Suffix $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/postgres/customer/username pia_cust_user_$Suffix $ETCDENDPOINT
$ETCDCTLCMD put $Prefix/postgres/customer/password pia_cust_pass_$Suffix $ETCDENDPOINT

# set symfony env
$ETCDCTLCMD put $Prefix/symfony/env $SYMFONYENV $ETCDENDPOINT 

$ETCDCTLCMD get --prefix $Prefix $ETCDENDPOINT

confd -onetime -backend etcdv3 -node http://${ETCDHOST}:2379 -confdir ./etc/confd -log-level debug -Prefix $Prefix
