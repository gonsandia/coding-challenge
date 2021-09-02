.PHONY: cs-fix
cs-fix: ### fix psr-12 code standards
	./vendor/bin/php-cs-fixer fix src --diff --rules=@PSR12
	./vendor/bin/php-cs-fixer fix tests --diff --rules=@PSR12

.PHONY: test
test: ### run unit testing
	./vendor/bin/phpunit \
		--exclude-group='disabled' --testdox tests
