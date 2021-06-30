.PHONY: clean code-check fix-code-style code-style dev-static-analysis static-analysis infection-testing test coverage install-dependencies update-dependencies help
.DEFAULT_GOAL := test

INFECTION = ./vendor/bin/infection
PHPUNIT = ./vendor/bin/phpunit -c ./phpunit.xml
PHPSTAN = ./vendor/bin/phpstan analyse
PHPCS = ./vendor/bin/phpcs --extensions=php
PHPCBF = ./vendor/bin/phpcbf --standard=PSR12

clean:
	rm -rf ./vendor

code-check:
	${PHPCS}
	${PHPSTAN}

fix-code-style:
	${PHPCBF} ./src ./tests
	${PHPCSF} . --rules=no_unused_imports

code-style:
	mkdir -p build/logs/phpcs
	${PHPCS}

dev-static-analysis:
	mkdir -p build/logs/phpstan
	${PHPSTAN}

static-analysis:
	mkdir -p build/logs/phpstan
	${PHPSTAN} --no-progress --error-format=junit | tee build/logs/phpstan/junit.xml

infection-testing:
	make coverage
	cp -f build/logs/phpunit/junit.xml build/logs/phpunit/phpunit.junit.xml
	${INFECTION} --coverage=build/logs/phpunit --min-msi=100 --threads=`nproc`

test:
	${PHPUNIT} --no-coverage

coverage:
	${PHPUNIT} && ./vendor/bin/coverage-check build/logs/phpunit/clover.xml 100

install-dependencies:
	composer install

update-dependencies:
	composer update

help:
	# Usage:
	#   make <target> [OPTION=value]
	#
	# Targets:
	#   clean                   Cleans the coverage and the vendor directory
	#   code-check              Check code style using phpcs & Code analysis
	#   code-style              Check code style using phpcs
	#   coverage                Code Coverage display
	#   fix-code-style          PHP Code fix using phpcbf
	#   help                    You're looking at it!
	#   infection-testing       Run infection/mutation testing
	#   install-dependencies    Install dependencies
	#   update-dependencies     Run composer update
	#   static-analysis         Run static analysis using phpstan (for CI)
	#   dev-static-analysis     Run static analysis using phpstan
	#   test                    Run tests
