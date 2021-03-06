#!/bin/bash

workdir=/home/sysad/manage

#Need this to get changelogs
if [ ! -x /usr/bin/apt-listchanges ]; then
	apt-get -y install apt-listchanges sqlite3
fi
if [ ! -x /usr/bin/sqlite3 ]; then
        apt-get -y install sqlite3
fi
if [ ! -x /usr/bin/sudo ]; then
        apt-get -y install sudo
fi
#Create Work dir if not exist
if [ ! -d /home/sysad/manage/ ]; then
        mkdir -p /home/sysad/manage/
fi


if [ ! -f /home/sysad/manage/synx.db ]; then
	STRUCTURE="CREATE TABLE Packages (package TEXT,date TEXT, time TEXT, rc INT, ii INT, upgrade INT, security INT, changelog TEXT, cversion TEXT, nversion TEXT,md5 TEXT,cpua TEXT, cpu TEXT,cput TEXT, cpuc TEXT, cpuf TEXT, cpus TEXT, RAM TEXT);";
	echo $STRUCTURE |sudo sqlite3 /home/sysad/manage/synx.db
fi

#Perform checks of all available packages for upgrade
#if [ $1 == "check" ];then
hwstats () {
        cpu=`lscpu |grep -E 'Architecture|CPU\(s\)|Thread|Core|CPU MHz|Socket'|grep -v list |grep -v NUMA |awk -F: '{print$2}'`
        arch=`echo $cpu|awk '{print$1}'`
        cpun=`echo $cpu|awk '{print$2}'`
        cput=`echo $cpu|awk '{print$3}'`
        cpuc=`echo $cpu|awk '{print$4}'`
        cpus=`echo $cpu|awk '{print$5}'`
        cpuf=`echo $cpu|awk '{print$6}'`
        mem=$(echo "scale=2; $(free -m |grep Mem|awk '{print$2}') / 1024" |bc )
        printf "UPDATE Packages SET cpua = '$arch', cpu = '$cpun', cput = '$cput', cpuc = '$cpuc', cpuf = '$cpuf', cpus = '$cpus', RAM = '$mem';"|sudo sqlite3 $workdir/synx.db
}
check () {
	apt-get update
	apt-get upgrade --just-print|grep  ^Inst |awk '{print$2,$3,$4}' |sed s'/\[//'|sed s'/\]//'|sed s'/(//'| while read -r a ;do
                pack=`echo $a|awk '{print$1}'`
                cver=`echo $a|awk '{print$2}'`
                nver=`echo $a|awk '{print$3}'`
                printf "UPDATE Packages SET upgrade = 1, cversion = '$cver', nversion = '$nver' WHERE package = '$pack';"|sudo sqlite3 $workdir/synx.db
        done

#fi
}
#Get changelogs for packages
#if [ $1 == "changelog" ];then
changelog () {
	cd /var/cache/apt/archives/;
	apt-get autoclean;
	packs=$(printf "SELECT package FROM Packages;"|sudo sqlite3 /home/sysad/manage/synx.db)
	apt-get download $packs;
	for ch in $(echo $packs);do
		ver=$(dpkg-query -s $ch|grep ^Version|awk -F: '{print$2,":",$3}' |sed s'/ //g'|sed s'/:$//g')
		printf "UPDATE Packages SET cversion = '$ver' WHERE package = '$ch';"|sudo sqlite3 $workdir/synx.db
	done
	rm -f $workdir/changelog.*
	for a in `ls /var/cache/apt/archives/*.deb`;do
		changelog=$(apt-listchanges -f text -a $a|grep -v "Reading changelogs"|sed s"/'//"|head -30)
		package=$(dpkg-deb --show $a|awk '{print$1}')
		printf "UPDATE Packages SET changelog = '$changelog' WHERE package = '$package';" |sudo sqlite3 $workdir/synx.db
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
		printf "UPDATE Packages SET security = 1, cversion = '$cver', nversion = '$nver' WHERE package = '$pack';"|sudo sqlite3 $workdir/synx.db
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
			printf "UPDATE Packages SET md5 = '$md5' WHERE package = '$Package';"|sudo sqlite3 $workdir/synx.db
        done
#fi
}
#get all packages installed
#if [ $1 == "inst" ];then
inst () {
	cd $workdir
	printf 'DELETE FROM Packages;'|sudo sqlite3 $workdir/synx.db
        find /var/lib/dpkg/info -name "*.list" |xargs stat -c $'%n\t%y' |     sed -e 's,/var/lib/dpkg/info/,,' -e 's,\.list\t,\t,' |    sort -n |awk '{print$1, $2, $3}' |sed -e 's/.000000000//g'|sed -e 's/:amd64//g'|sed "s/ /,/g"|sed "s/$/,,,,,,,,,,,,,,,/" > $workdir/previous
	printf ".separator , \n.import $workdir/previous Packages" |sudo sqlite3 $workdir/synx.db
	for a in `cat /home/sysad/manage/previous |awk -F, '{print$1}'` ; do
		for b in `dpkg -l|grep -w $a |grep -v ^ii |awk '{print$2}'`;do
		printf "UPDATE Packages SET rc = 1 WHERE package = '$b';"|sudo sqlite3 $workdir/synx.db
		done
	done
	printf "UPDATE Packages SET ii = 1 WHERE rc != 1;"|sudo sqlite3 $workdir/synx.db
#fi
}
logs () {
	echo "deb http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main" > /etc/apt/sources.list.d/webupd8team-java.list
	apt-key adv --keyserver keyserver.ubuntu.com --recv-keys EEA14886
	echo 'deb http://packages.elasticsearch.org/elasticsearch/1.4/debian stable main' | sudo tee /etc/apt/sources.list.d/elasticsearch.list
	echo 'deb http://packages.elasticsearch.org/logstash/1.5/debian stable main' | sudo tee /etc/apt/sources.list.d/logstash.list
	wget -O - http://packages.elasticsearch.org/GPG-KEY-elasticsearch | sudo apt-key add -
	apt-get update	
	export DEBIAN_FRONTEND=noninteractive; apt-get -y install oracle-java8-installer elasticsearch=1.4.4 logstash 
	update-rc.d elasticsearch defaults 95 10
	sed -i '/network.host: /c\network.host: 127.0.0.1' /etc/elasticsearch/elasticsearch.yml
	cd /opt/; wget https://download.elasticsearch.org/kibana/kibana/kibana-4.1.1-linux-x64.tar.gz
	tar zxpvf kibana-4.1.1-linux-x64.tar.gz
	ln -s kibana-4.1.1-linux-x64 kibana
	cd /etc/init.d && sudo wget https://gist.githubusercontent.com/thisismitch/8b15ac909aed214ad04a/raw/bce61d85643c2dcdfbc2728c55a41dab444dca20/kibana4
	chmod +x /etc/init.d/kibana4
	update-rc.d kibana4 defaults 96 9
	service kibana4 start	
	htpasswd -c /etc/nginx/htpasswd.users bgeach
	
		
	
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
	hwstats)
	    hwstats
	    ;;
	logs)
	    logs
	    ;;
        *)
            echo $"Usage: $0 {all|check|md5|changelog|inst}"
            exit 1

esac
