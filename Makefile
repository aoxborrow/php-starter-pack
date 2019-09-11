default: start

start:
	sudo service php-fpm start
	sudo service nginx start

restart:
	sudo service php-fpm restart
	sudo service nginx restart

stop:
	sudo service nginx stop
	sudo service php-fpm stop

install-galaxy-roles:
	ansible-galaxy install --roles-path ansible/roles -r ansible/requirements.yml

provision-dev:
	ansible-playbook -c local -i ansible/dev ansible/playbook.yml

provision-prod:
	ansible-playbook -c local -i ansible/prod ansible/playbook.yml
