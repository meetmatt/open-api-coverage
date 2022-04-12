# TODO

## Missing features
### Parser
- anyOf
- oneOf
- non-object allOf/anyOf/oneOf
- match path/method against explicit operation ID (coversPath/Operation/etc.)
- logging facility
- strict mode
- test compat with php 7 and 8
- match array parameters by value (param[0] === param[])

### Printer
- Response coverage
- Media type

### Coverage report
- CLI report
- HTML report

## Test coverage
### Functional tests
- Query paramaters
- Request body
- Response status code
- Response body

### Unit tests
- Parser
- Coverage with simpler specs

### Integration tests
- Codeception module integration test

## Known issues
- Coverage::diffTypes has side-effects (marking as executed/documented)
