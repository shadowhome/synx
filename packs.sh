#!/bin/bash

#update myself
wget https://raw.githubusercontent.com/shadowhome/synx/master/packs.sh -O /home/sysad/manage/packs.sh; chmod 700 /home/sysad/manage/packs.sh

workdir=/home/sysadmin/manage/

#Need this to get changelogs
if [ ! -x /usr/bin/apt-listchanges ];then
        apt-get -y install apt-listchanges
fi

#Create Work dir if not exist
if [ ! -d /home/sysadmin/manage/ ]; then
        mkdir -p /home/sysadmin/manage/
fi

#Perform checks of all available packages for upgrade
if [ $1 == "check" ];then
        rm -f $workdir/apacks.txt
        apt-get --just-print upgrade 2>&1 | perl -ne 'if (/Inst\s([\w,\-,\d,\.,~,:,\+]+)\s\[([\w,\-,\d,\.,~,:,\+]+)\]\s\(([\w,\-,\d,\.,~,:,\+]+)\)? /i) {print "PROGRAM: $1 INSTALLED: $2 AVAILABLE: $3\n"}' > $workdir/apacks.txt
fi

#Get changelogs and md5 for packages
if [ $1 == "changelog" ];then
        cd /var/cache/apt/archives/;
        apt-get autoclean;
        apt-get download $2;
        rm -f $workdir/changelog.*
        for a in `ls *.deb`
                do echo "START"; apt-listchanges $a |grep -v "Reading changelogs" ;echo "ENDED"
        done
fi

#Check which of these are security
if [ $1 == "security" ];then
        rm -f $workdir/spacks.txt
        apt-get upgrade -s|grep Debian-Security|grep ^Inst |awk '{print"name:",$2,"Current:",$3,"New:"$4}' > /home/sysadmin/manage/spacks.txt
fi

#Get md5
if [ $1 == "md5" ];then
        cd /var/cache/apt/archives/;
        for a in `ls *.deb`
                do dpkg-deb -I $a|grep Package ;md5sum $a;tr -d '\n'
        done
fi
