install:
	composer install

diff:
	./bin/gendiff

validate:
	composer validate

autoload:
	composer -o dump-autoload

lint:
	composer run-script phpcs -- --standard=PSR12 src bin tests
stan:
	composer exec -v phpstan analyse -- -c ./phpstan.neon --ansi

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 src bin tests

test:
	composer exec --verbose phpunit tests

test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xmlma