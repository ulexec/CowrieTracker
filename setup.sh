# /bin/bash
echo "[*] Diontrack Installer v1.0. Tested in Ubuntu x64 14.04 LTS"

if [ "$(id -u)" != 0 ]
then
	echo "[!] Run script as sudo"
else
	sudo apt-get install git virtualenv libmpfr-dev libssl-dev libmpc-dev libffi-dev build-essential libpython-dev authbind python-virtualenv -yq

	sed -i 's/22/8742/g' /etc/ssh/sshd_config

	adduser cowrie 
	
	touch /etc/authbind/byport/22
	chown cowrie:cowrie /etc/authbind/byport/22
	chmod 777 /etc/authbind/byport/22


	cd cowrie
	echo 'git clone https://github.com/cowrie/cowrie.git'
	echo 'cp cowrie.cfg.dist cowrie.cfg'

	service ssh restart

	su cowrie
fi

