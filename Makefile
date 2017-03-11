NAME = phpda

default: clean build cp-artifacts test

clean:
	-docker rm -fv $(NAME)-php56
	-focker rm -fv $(NAME)-php70
	-docker rm -fv $(NAME)-hhvm
	-docker rm -fv $(NAME)
	-docker rmi -f $(NAME)-php56
	-docker rmi -f $(NAME)-php70
	-docker rmi -f $(NAME)-hhvm
	-docker rmi -f $(NAME)
	-rm -rf vendor

build:
	docker build -t $(NAME)-php56 -f Dockerfile-php56 .
	docker build -t $(NAME)-php70 -f Dockerfile-php70 .
	docker build -t $(NAME)-hhvm -f Dockerfile-hhvm .
	docker build -t $(NAME) -f Dockerfile .

cp-artifacts:
	docker create --name $(NAME) $(NAME)
	docker cp $(NAME):/app/vendor ./
	docker rm -fv $(NAME)

test:
	docker run --rm -it -v $(shell pwd):/app $(NAME)-php56 composer test
	docker run --rm -it -v $(shell pwd):/app $(NAME)-php70 composer test
	-docker run --rm -it -v $(shell pwd):/app $(NAME)-hhvm hhvm ./vendor/bin/codecept run
	docker run --rm -it -v $(shell pwd):/app $(NAME) composer test
