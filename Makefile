default: start

db:
	mysql < db.sql

start:
	sudo service php7.3-fpm start
	sudo service nginx start

restart:
	sudo service php7.3-fpm restart
	sudo service nginx restart

stop:
	sudo service nginx stop
	sudo service php7.3-fpm stop

install-galaxy-roles:
	ansible-galaxy install --roles-path ansible/roles -r ansible/requirements.yml

provision-dev:
	ansible-playbook -i ansible/inventory.yml -l dev ansible/playbook.yml

provision-prod:
	ansible-playbook -i ansible/inventory.yml -l prod ansible/playbook.yml --ask-pass
