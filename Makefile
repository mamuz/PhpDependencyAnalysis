NAME = phpda

default: clean image vendor

clean:
	-docker rm -fv $(NAME)-php56
	-docker rm -fv $(NAME)-php70
	-docker rm -fv $(NAME)-hhvm
	-docker rm -fv $(NAME)-php71
	-docker rmi -f $(NAME)-php56
	-docker rmi -f $(NAME)-php70
	-docker rmi -f $(NAME)-hhvm
	-docker rmi -f $(NAME)-php71
	-rm -rf vendor

image:
	docker build -t $(NAME)-php56 -f ./build/Dockerfile-php56 .
	docker build -t $(NAME)-php70 -f ./build/Dockerfile-php70 .
	docker build -t $(NAME)-hhvm -f ./build/Dockerfile-hhvm .
	docker build -t $(NAME)-php71 -f ./build/Dockerfile-php71 .

vendor:
	docker create --name $(NAME)-php71 $(NAME)-php71
	docker cp $(NAME)-php71:/app/vendor ./
	docker rm -fv $(NAME)-php71

test:
	docker run --rm -it -v $(shell pwd):/app $(NAME)-php56 composer test
	docker run --rm -it -v $(shell pwd):/app $(NAME)-php70 composer test
	-docker run --rm -it -v $(shell pwd):/app $(NAME)-hhvm hhvm ./vendor/bin/codecept run
	docker run --rm -it -v $(shell pwd):/app $(NAME)-php71 composer test

release:
	docker run --rm -it -v $(shell pwd):/app $(NAME)-php71 composer phar
