NAME = phpda

default: clean build cp-artifacts test

build:
	docker build -t $(NAME)-php56 -f Dockerfile-php56 .
	docker build -t $(NAME)-php70 -f Dockerfile-php70 .
	docker build -t $(NAME)-hhvm -f Dockerfile-hhvm .

cp-artifacts:
	@docker create --name $(NAME)-php70 $(NAME)-php70
	@docker cp $(NAME)-php70:/phpda/vendor ./
	@docker rm -fv $(NAME)-php70

clean:
	-@docker rm -fv $(NAME)-php56
	-@docker rm -fv $(NAME)-php70
	-@docker rmi -f $(NAME)-php56
	-@docker rmi -f $(NAME)-php70
	-@docker rmi -f $(NAME)-hhvm
	-@docker rmi -f $(NAME)-hhvm
	-@rm -rf vendor

test:
	@docker run --rm -it -v $(shell pwd):/phpda $(NAME)-php56 ./vendor/bin/codecept run
	@docker run --rm -it -v $(shell pwd):/phpda $(NAME)-php70 ./vendor/bin/codecept run
	@docker run --rm -it -v $(shell pwd):/phpda $(NAME)-hhvm ./vendor/bin/codecept run
