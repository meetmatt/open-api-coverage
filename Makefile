.PHONY: all
all:
	$(MAKE) test suite=unit

.PHONY: test
test:
	XDEBUG_MODE=$(debug),coverage XDEBUG_SESSION=1 ./vendor/bin/codecept run $(suite) $(test) $(params) $(coverage)
