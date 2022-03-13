test: unit-test integration-test

unit-test:
	XDEBUG_MODE=$(debug),coverage XDEBUG_SESSION=1 ./vendor/bin/codecept run unit $(params) $(coverage) $(test)

integration-test:
	XDEBUG_MODE=$(debug),coverage XDEBUG_SESSION=1 ./vendor/bin/codecept run integration $(params) $(coverage) $(test)
