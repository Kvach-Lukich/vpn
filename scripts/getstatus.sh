#!/bin/bash
cd "$(dirname "${BASH_SOURCE[0]}")"
if [[ ! -f "/run/getstatuspid" ]];then
   echo $$>/run/getstatuspid
   sleep 30
   REZ=0
   while [[ "$REZ" != "1" ]]
   do
      REZ=`/usr/bin/php getstatus.php`
      #echo $REZ>>stat.log
      sleep 2
   done
fi
