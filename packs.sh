#!/bin/bash

workdir=/home/sysad/manage

#Need this to get changelogs
if [ ! -x /usr/bin/apt-listchanges ]; then
	apt-get -y install apt-listchanges sqlite3
fi
if [ ! -x /usr/bin/sqlite3 ]; then
        apt-get -y install sqlite3
fi

#Create Work dir if not exist
if [ ! -d /home/sysad/manage/ ]; then
        mkdir -p /home/sysad/manage/
fi

if [ ! -f /home/sysad/manage/synx.db ]; then
	STRUCTURE="CREATE TABLE Packages (package TEXT,date TEXT, time TEXT, rc INT, ii INT, upgrade INT, security INT, changelog TEXT, cversion TEXT, nversion TEXT,md5 TEXT,cpua TEXT, cpu TEXT,cput TEXT, cpuc TEXT, cpuf TEXT);";
	echo $STRUCTURE |sqlite3 /home/sysad/manage/synx.db
fi

#Perform checks of all available packages for upgrade
#if [ $1 == "check" ];then
hwstats () {
	cpu=`lscpu |grep -E 'Architecture|CPU\(s\)|Thread|Core|CPU MHz' |awk '{print$2}'`
	arch=`echo $cpu|awk '{print$1}'`
	cpun=`echo $cpu|awk '{print$2}'`
	cput=`echo $cpu|awk '{print$3}'`
	cpuc=`echo $cpu|awk '{print$4}'`
	cpuf=`echo $cpu|awk '{print$5}'`
	mem=`free -m |grep Mem|awk '{print$2}'`
	printf "UPDATE Packages SET cpua = '$arch', cpu = '$cpun', cput = '$cput', cpuc = '$cpuc', cpuf = '$cpuf';"|sqlite3 $workdir/synx.db
}
check () {
	apt-get update
	apt-get upgrade --just-print|grep  ^Inst |awk '{print$2,$3,$4}' |sed s'/\[//'|sed s'/\]//'|sed s'/(//'| while read -r a ;do
                pack=`echo $a|awk '{print$1}'`
                cver=`echo $a|awk '{print$2}'`
                nver=`echo $a|awk '{print$3}'`
                printf "UPDATE Packages SET upgrade = 1, cversion = '$cver', nversion = '$nver' WHERE package = '$pack';"|sqlite3 $workdir/synx.db
        done

#fi
}
#Get changelogs for packages
#if [ $1 == "changelog" ];then
changelog () {
	cd /var/cache/apt/archives/;
	apt-get autoclean;
	packs=$(printf "SELECT package FROM Packages;"|sqlite3 /home/sysad/manage/synx.db)
	echo $pakcs
	apt-get download $packs;
	rm -f $workdir/changelog.*
	for a in `ls /var/cache/apt/archives/*.deb`;do
		changelog=$(apt-listchanges -f text -a $a|grep -v "Reading changelogs"|sed s"/'//"|head -30)
		package=$(dpkg-deb --show $a|awk '{print$1}')
		printf "UPDATE Packages SET changelog = '$changelog' WHERE package = '$package';" |sqlite3 $workdir/synx.db
	done
#fi
}
#Check which of these are security
#if [ $1 == "security" ];then
security () {
	apt-get upgrade -s|grep Debian-Security|grep ^Inst |awk '{print$2,$3,$4}' |sed s'/\[//'|sed s'/\]//'|sed s'/(//'| while read -r a ;do
		pack=`echo $a|awk '{print$1}'`
		cver=`echo $a|awk '{print$2}'`
		nver=`echo $a|awk '{print$3}'`
		printf "UPDATE Packages SET security = 1, cversion = '$cver', nversion = '$nver' WHERE package = '$pack';"|sqlite3 $workdir/synx.db
	done
#fi
}

#Get md5
#if [ $1 == "md5" ];then
md5 () {
        cd /var/cache/apt/archives/;
        for a in `ls *.deb`;do
		Package=`dpkg-deb -I $a|grep Package|awk '{print$2}'`
		md5=`md5sum $a|awk '{print$1}'`
			printf "UPDATE Packages SET md5 = '$md5' WHERE package = '$Package';"|sqlite3 $workdir/synx.db
        done
#fi
}
#get all packages installed
#if [ $1 == "inst" ];then
inst () {
	cd $workdir
	printf 'DELETE FROM Packages;'|sqlite3 $workdir/synx.db
        find /var/lib/dpkg/info -name "*.list" -exec stat -c $'%n\t%y' {} \; |     sed -e 's,/var/lib/dpkg/info/,,' -e 's,\.list\t,\t,' |    sort -n |awk '{print$1, $2, $3}' |sed -e 's/.000000000//g'|sed -e 's/:amd64//g'|sed "s/ /,/g"|sed "s/$/,,,,,,,,,,,,,,,,,,/" > $workdir/previous
	printf ".separator , \n.import $workdir/previous Packages" |sqlite3 $workdir/synx.db
	for a in `cat /home/sysad/manage/previous |awk -F, '{print$1}'` ; do
		for b in `dpkg -l|grep -w $a |grep -v ^ii |awk '{print$2}'`;do
		printf "UPDATE Packages SET rc = 1 WHERE package = '$b';"|sqlite3 $workdir/synx.db
		done
	done
	printf "UPDATE Packages SET ii = 1 WHERE rc != 1;"|sqlite3 $workdir/synx.db
#fi
}
all () {
	inst
	check
	security
	md5
	changelog
	hwstats
}


case "$1" in
        all)
            all
            ;;

        check)
            check
            ;;

        md5)
            md5
            ;;
        changelog)
            changelog
            ;;
        inst)
	    inst
            ;;

        *)
            echo $"Usage: $0 {all|check|md5|changelog|inst}"
            exit 1

esac
