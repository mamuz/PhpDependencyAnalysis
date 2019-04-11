SHELL:=/bin/bash
NAME = phpda

.PHONY: all
all: build-dev test ## Build and test

.PHONY: build-dev
build-dev: clean ## Build a new docker image for development.
	docker build -t $(NAME) -f ./Dockerfile-dev .
	docker create --name $(NAME) $(NAME)
	docker cp $(NAME):/app/vendor ./
	docker cp $(NAME):/app/composer.lock ./composer.lock
	docker rm -fv $(NAME)

.PHONY: build-prod
build-prod: ## Build a new docker image for production.
	docker build -t $(NAME) .

.PHONY: clean
clean: ## Purge all related artifacts.
	-@docker rm -fv $(NAME) 2>/dev/null
	-@rm -rf ./vendor 2>/dev/null

.PHONY: test
test: ## Run all tests.
	docker run --rm -it -v $(shell pwd):/app $(NAME) bash -c "composer test"

.PHONY: help
help: ## Output this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'
