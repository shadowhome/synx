#!/bin/bash

workdir=/home/sysad/manage/

#Need this to get changelogs
if [ ! -x /usr/bin/apt-listchanges ];then
        apt-get -y install apt-listchanges
fi

#Cleanup as it will all get regenerated.
rm -f $workdir/*

#Create Work dir if not exist
if [ ! -d /home/sysad/manage/ ]; then
        mkdir -p /home/sysad/manage/
fi

#Perform checks of all available packages for upgrade
if [ $1 == "check" ];then
        apt-get --just-print upgrade 2>&1 | perl -ne 'if (/Inst\s([\w,\-,\d,\.,~,:,\+]+)\s\[([\w,\-,\d,\.,~,:,\+]+)\]\s\(([\w,\-,\d,\.,~,:,\+]+)\)? /i) {print "PROGRAM: $1 INSTALLED: $2 AVAILABLE: $3\n"}' > /home/sysad/manage/apacks.txt
fi

#Get changelogs for packages
if [ $1 == "changelog" ];then
        cd /var/cache/apt/archives/;
        apt-get autoclean;
        apt-get download $2;
	for a in /var/cache/apt/archives/*.deb
                do apt-listchanges $a*.deb |grep -v "Reading changelogs" > $workdir/changelog.$a
        done
fi

#Check which of these are security
if [ $1 == "security" ];then
        apt-get upgrade -s|grep Debian-Security|grep ^Inst |awk '{print"name:",$2,"Current:",$3,"New:"$4}' > /home/sysad/manage/spacks.txt
fi

