#!/bin/bash

IP=$(ifconfig eth0 | awk -F"[: ]+" '/inet addr:/ {print $4}')
hostname=$(hostname -f)
webserver=$(netstat -ntpl|grep 80|grep -c nginx)
dir=$(pwd -P)

if [ ! -x /usr/bin/fail2ban-server ]; then
        apt-get -y install fail2ban
fi
if [ ! -x /usr/bin/sudo ]; then
        apt-get -y install sudo
fi
if [ ! -x /usr/bin/htpasswd ]; then
        apt-get -y install apache2-utils
fi

logs () {
        echo "deb http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main" > /etc/apt/sources.list.d/webupd8team-java.list
        apt-key adv --keyserver keyserver.ubuntu.com --recv-keys EEA14886
        echo 'deb http://packages.elasticsearch.org/elasticsearch/1.4/debian stable main' | sudo tee /etc/apt/sources.list.d/elasticsearch.list
        echo 'deb http://packages.elasticsearch.org/logstash/1.5/debian stable main' | sudo tee /etc/apt/sources.list.d/logstash.list
        wget -O - http://packages.elasticsearch.org/GPG-KEY-elasticsearch | sudo apt-key add -
        apt-get update

	#Install tools elasticache java and logstash
	echo debconf shared/accepted-oracle-license-v1-1 select true | debconf-set-selections
 	echo debconf shared/accepted-oracle-license-v1-1 seen true | debconf-set-selections
        export DEBIAN_FRONTEND=noninteractive; apt-get -y install oracle-java8-installer elasticsearch=1.4.4 logstash
	curl -XPUT http://localhost:9200/logstash-* -d '
		{
		"mappings" : {
		"log" : {
		"properties" : {
		"geo" : {
		"properties" : {
		"coordinates" : {
		"type" : "geo_point"
		}
		}
		}
		}
		}
		}
		}
	';
	service elasticsearch restart
        update-rc.d elasticsearch defaults 95 10
	#Configure elasticache
        sed -i '/network.host: /c\network.host: 127.0.0.1' /etc/elasticsearch/elasticsearch.yml
	
	#Install Kibana
        cd /opt/; wget https://download.elasticsearch.org/kibana/kibana/kibana-4.1.1-linux-x64.tar.gz
        tar zxpvf kibana-4.1.1-linux-x64.tar.gz
        ln -s kibana-4.1.1-linux-x64 kibana
        cd /etc/init.d && sudo wget https://gist.githubusercontent.com/thisismitch/8b15ac909aed214ad04a/raw/bce61d85643c2dcdfbc2728c55a41dab444dca20/kibana4
        chmod +x /etc/init.d/kibana4
        update-rc.d kibana4 defaults 96 9
        service kibana4 start

	#Setup web server for access
	if [ $webserver -ge 1 ];then
        htpasswd -b -c /etc/nginx/htpasswd.users kib kibadmin
	sed -i "/server_name/c\server_name $hostname;" $dir/src/nginx/kib
	cp $dir/src/nginx/kib /etc/nginx/sites-enabled/kib
	service nginx restart
	else
	htpasswd -b -c /etc/apache2/htpasswd.users kib kibadmin
	sed -i "/ServerName/c\ServerName $hostname" $dir/src/nginx/kibap
	cp $dir/src/nginx/kibap /etc/apache2/sites-enabled/
	service apache2 restart
	fi
	
	#Config logstash
	cp -r $dir/src/logstash/conf.d/* /etc/logstash/conf.d/
	mkdir -p /etc/pki/tls/certs
	mkdir /etc/pki/tls/private
	cd /etc/pki/tls
	sed -i "/\[ v3_ca \]/a\subjectAltName = IP:$IP" $dir/src/openssl/openssl.cnf
	openssl req -config $dir/src/openssl/openssl.cnf -x509 -days 3650 -nodes -newkey rsa:2048 -keyout /etc/pki/tls/private/logstash-forwarder.key -out /etc/pki/tls/certs/logstash-forwarder.crt -subj "/C=GB/ST=London/L=London/O=Global Security/OU=IT Department/CN=$hostname"
	if [ ! -d /opt/logstash/patterns/ ];then
	mkdir /opt/logstash/patterns/
	fi

	cat << 'EOF' >>  /opt/logstash/patterns/nginx
NGUSERNAME [a-zA-Z\.\@\-\+_%]+
NGUSER %{NGUSERNAME}
NGINXACCESS %{IPORHOST:clientip} %{NGUSER:ident} %{NGUSER:auth} \[%{HTTPDATE:timestamp}\] "%{WORD:verb} %{URIPATHPARAM:request} HTTP/%{NUMBER:httpversion}" %{NUMBER:response} (?:%{NUMBER:bytes}|-) (?:"(?:%{URI:referrer}|-)"|%{QS:referrer}) %{QS:agent}
EOF
	cd /etc/logstash/
	curl -O "http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz" 
	cd /etc/logstash; gzip -d GeoLiteCity.dat.gz
	service logstash restart

	



}
all () {
        logs
}


case "$1" in
        all)
            all
            ;;

        logs)
            logs
            ;;

        *)
            echo $"Usage: $0 {all|logs}"
            exit 1

esac
