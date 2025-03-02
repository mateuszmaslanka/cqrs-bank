.PHONY: start stop cli open open-console fix-permissions

start:
	docker-compose -f ./docker/docker-compose.yaml up -d

stop:
	docker-compose -f ./docker/docker-compose.yaml down

cli:
	docker-compose -f ./docker/docker-compose.yaml run --remove-orphans php-cli sh

open:
	export BROWSER='/mnt/c/Progra~2/Google/Chrome/Application/chrome.exe'
	sensible-browser http://localhost:8080/bank

open-console:
	xdg-open http://localhost:8080/bank

fix-permissions:
	sudo chown $USER:$USER -R ./source/*
