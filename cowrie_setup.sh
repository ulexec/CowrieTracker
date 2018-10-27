#!/bin/sh
echo "[*] Cowrie setup script"

if [ "$(id -u)" != 0 ]
then
	echo "[-] This script must be run as sudo"
else
	#installing dependencies
	apt-get update -yq && apt-get upgrade -yq
	apt-get install tasksel -yq
	apt-get install sqlite3 python build-essential python-dev php-sqlite3 -yq
	apt-get install git virtualenv libmpfr-dev libssl-dev libmpc-dev authbind python-virtualenv -yq
	tasksel install lamp-server

	#cloning cowrie repository
	git clone http://github.com/cowrie/cowrie /opt/cowrie
	cd /opt/cowrie

	#changing cowrie config
	cp ./etc/cowrie.cfg.dist ./cowrie.cfg
	
	#sed -i '/hostname = svr04/c\hostname = gimmethembots' cowrie.cfg
	#sed -i '/#listen_endpoints = tcp:2222:interface=0.0.0.0/c\listen_endpoints = tcp:22:interface=0.0.0.0' cowrie.cfg
	#sed -i '/# Enable Telnet support, disabled by default\nenabled = false/c\# Enable Telnet support, disabled by default\nenabled = true' cowrie.cfg
	#sed -i '/# (default: 2222)\n#listen_port = 2222/c\# (default: 2222)\nlisten_port = 22' cowrie.cfg
	#sed -i '/# (default: 2223)\n#listen_port = 2223/c\# (default: 2223)\nlisten_port = 23' cowrie.cfg
	#sed -i '/#[output_sqlite]/c\[output_sqlite]' cowrie.cfg
	#sed -i '/#enabled = false\n#db_file = cowrie.db/c\enabled = true\ndb_file = cowrie.db' cowrie.cfg

	#initialising cowrie sqlite dataabse
	sqlite3 cowrie.db < docs/sql/sqlite3.sql

	#creating new user cowrie
	useradd cowrie -s /bin/bash

	#changing permissions of current directory
	chown -R cowrie:users /opt/cowrie/

	#setting up a new port for ssh login (8742)
	touch /etc/authbind/byport/22
	chown cowrie /etc/authbind/byport/22
	chmod 770 /etc/authbind/byport/22
	sed -i 's/22/8742/g' /etc/ssh/sshd_config

	#restarting ssh and web servers
	systemctl restart ssh
	systemctl restart apache2


	echo '[+] Done. Now execute this:'
	echo '\tsu cowrie'
	echo '\tvirtualenv cowrie-env'
	echo '\tsource cowrie-env/bin/activate'
	echo '\tpip install -r requirements.txt'
	echo '\t./bin/cowrie start'
fi

