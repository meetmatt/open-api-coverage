test: unit-test integration-test

unit-test:
	./vendor/bin/codecept run unit

integration-test:
	./vendor/bin/codecept run integration
