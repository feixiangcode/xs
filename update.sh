#!/bin/sh
cd /data/www/xs/$2
newest=`ls | egrep "^[0-9]" | sort -n | tail -1`
cd /data/www/xs/
php UpdateCommand.php $1 $2 $3 $newest
