SHELL:=/bin/bash
NAME = phpda

.PHONY: all
all: build test ## Build and test

.PHONY: build
build: clean ## Build a new docker image.
	docker build -t $(NAME) -f ./build/Dockerfile .
	docker create --name $(NAME) $(NAME)
	docker cp $(NAME):/app/vendor ./
	docker cp $(NAME):/app/composer.lock ./composer.lock
	docker rm -fv $(NAME)

.PHONY: clean
clean: ## Purge all related artifacts.
	-@docker rm -fv $(NAME) 2>/dev/null
	-@rm -rf ./vendor 2>/dev/null

.PHONY: test
test: ## Run all tests.
	docker run --rm -it -v $(shell pwd):/app $(NAME) sh -c "composer test"

.PHONY: help
help: ## Output this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'
