#!/bin/bash

#Get the cert
os=$(lsb_release -c|awk  '{printf$2}')

cert=$(cat /etc/pki/tls/certs/logstash-forwarder.crt)
echo 'deb http://packages.elasticsearch.org/logstashforwarder/debian stable main' | sudo tee /etc/apt/sources.list.d/logstashforwarder.list
wget -O - http://packages.elasticsearch.org/GPG-KEY-elasticsearch | sudo apt-key add -

wget http://ossec.wazuh.com/repos/apt/debian/pool/main/o/ossec-hids/ossec-hids_2.8.2-2$(echo $os)_amd64.deb
dpkg -i ossec-hids_2.8.2-2$(echo $os)_amd64.deb
apt-get update
apt-get install logstash-forwarder 

