#!/usr/bin/env bash
set -ex

export ETCDCTL_API=3

# rand number to avoid build colision (same db used by two build)
if [ ! -f shuf.nbr ]
then
    shuf -i 200-600 -n 1 > shuf.nbr
fi

#RND may contain branch with '-' or upper case char which may not work as database name for postgre
suffix=$(echo $RND|sed -e s/-/_/g|tr '[:upper:]' '[:lower:]')$(echo -n $(cat shuf.nbr ))
prefix="/pia/build/$suffix"

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
$ETCDCTLCMD get  --prefix '/default' $ETCDENDPOINT

# get postgres default
postgreshost=$($ETCDCTLCMD get /default/postgres/hostname --print-value-only $ETCDENDPOINT)
postgresuser=$($ETCDCTLCMD get /default/postgres/root/username --print-value-only $ETCDENDPOINT)
postgrespass=$($ETCDCTLCMD get /default/postgres/root/password --print-value-only $ETCDENDPOINT)
# TODO add a check default cnx with psql

# set postgres env
$ETCDCTLCMD put $prefix/postgres/hostname $postgreshost $ETCDENDPOINT
$ETCDCTLCMD put $prefix/postgres/root/username $postgresuser $ETCDENDPOINT
$ETCDCTLCMD put $prefix/postgres/root/password $postgrespass $ETCDENDPOINT

$ETCDCTLCMD put $prefix/postgres/oauth/dbname pia_oauth_db_$suffix $ETCDENDPOINT
$ETCDCTLCMD put $prefix/postgres/oauth/username pia_oauth_user_$suffix $ETCDENDPOINT
$ETCDCTLCMD put $prefix/postgres/oauth/password pia_oauth_pass_$suffix $ETCDENDPOINT

$ETCDCTLCMD put $prefix/postgres/customer/dbname pia_cust_db_$suffix $ETCDENDPOINT
$ETCDCTLCMD put $prefix/postgres/customer/username pia_cust_user_$suffix $ETCDENDPOINT
$ETCDCTLCMD put $prefix/postgres/customer/password pia_cust_pass_$suffix $ETCDENDPOINT

# set symfony env
$ETCDCTLCMD put $prefix/symfony/env $SYMFONYENV $ETCDENDPOINT 

$ETCDCTLCMD get --prefix $prefix $ETCDENDPOINT

confd -onetime -backend etcdv3 -node http://${ETCDHOST}:2379 -confdir ./etc/confd -log-level debug -prefix $prefix
