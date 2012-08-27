test:
	cd mockapi ; npm install
	node mockapi/app.js &
	phpunit tests/GeneralTest.php
	kill `cat /tmp/mockapi.pid` 
	
.PHONY: test
