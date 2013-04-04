clean:
	@-rm composer.phar
	@-rm *.log
	@-rm -rf vendor
	@-rm /tmp/mockapi.pid

composer-download:
	@-rm composer.phar
	curl -sS https://getcomposer.org/installer | php

composer-install:
	composer.phar install --dev

composer-update:
	composer.phar update --dev

install-dev: clean composer-download composer-install

mock-server:
	@-killall node
	@cd mockapi ; npm install > ../npm-install.log
	@node mockapi/app.js & > mock-server.log

phpunit: mock-server
	vendor/bin/phpunit tests/GeneralTest.php
	@kill `cat /tmp/mockapi.pid`
	@-rm /tmp/mockapi.pid;

test:  phpunit

.PHONY: test
