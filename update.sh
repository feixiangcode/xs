#!/bin/sh
cd /data/www/xs/$1
newest=`ls | egrep "^[0-9]" | sort | tail -1`
cd /data/www/xs/
php UpdateCommand.php kujiang $1 $2 $newest
