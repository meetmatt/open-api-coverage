# OpenAPI specification coverage

## Purpose

This package can be used to report the documented and record the executed parts of
 an Open API specification under test: which paths and operations were
 executed by the test, which parameters were passed, which content types
 were sent, which response status code classes, codes, body properties and
 content types were returned and asserted.

## Glossary

* Documented / undocumented: present in the specification / not present.
* Passed / not passed: sent in the request / not sent.
* Obtained / not obtained: received in the response / not received.
* Tested / not tested: obtained response was asserted against the expected
value / not asserted or the given assertion failed.
* TCL: Test Coverage Level (per suite, per specification).
* Input criteria: related to the API requests.
* Output criteria: related to the API responses.

## Criteria

### Input criteria

Types of input criteria:
* Input parameters (optional or mandatory).
* Input parameter values (enums, booleans).
* Content-types (only applicable for operations with request body).
* Operation flows (not implemented, lacks formal definitions).

Request coverage:
1. Documented, passed - fully covered.
2. Documented, not passed - lacks API call.
3. Undocumented, passed - lacks documentation.
4. (Nonsense) Undocumented, not passed.

### Output criteria

Types of output criteria:
* Status code classes: 200 vs 400/500, should be explicitly defined.
* Status codes.
* Response body properties.
* Content-types.

Response coverage:
1. Documented, obtained, tested - fully covered.
2. Documented, not obtained, testing not applicable - lacks API call and tests.
3. Documented, obtained, not tested - lacks tests.
4. Undocumented, obtained, tested - lacks documentation.
5. Undocumented, obtained, not tested - lacks documentation and tests.
6. (Nonsense) Undocumented, not obtained, testing not relevant.

## Test Coverage Levels (TCL)
 
| TCL | Input criteria  | Output criteria          |  
| --- | --------------- | ------------------------ |  
|  0  |                 |                          |  
|  1  | Paths           |                          |  
|  2  | Operations      |                          |  
|  3  | Content-type    | Content-type             |  
|  4  | Parameters      | Status code class        |  
|  5  |                 | Status codes             |  
|  6  |                 | Response body properties |  
|  7  | Operation flows |                          |

## Object Model

### Coverage

```yaml
Coverage:
  Spec: Specification
  Input: InputCriteria
  Output: OutputCriteria
```

### Specification

```yaml
Specification:
  ID: string
  Paths: URL -> Path
    URL: string
    Operations: HTTP Method -> Operation
      HTTP Method: string
      PathParameters: Name -> Values
        Name: string
        Values: array
      QueryParameters: Name -> Values
        Name: string
        Values: array
      RequestBodies: Content-type -> RequestBody
        Content-type: string
        Properties: Path -> Values
          Path: string
          Values: array
      Responses: HTTP Status Code -> Response
        HTTP Status Code: string
        ResponseBodies: Content-type -> ResponseBody
          Content-type: string
          Properties: Path -> Values
            Path: string
            Values: array
```

## References

[A. Martin-Lopez, S. Segura, A. Ruiz-Cortés. 2019. **Test Coverage Criteria for RESTful Web APIs.** ACM SIGSOFT International Workshop on Automating TEST Case Design, Selection, and Evaluation (A-TEST'19).](https://personal.us.es/amarlop/wp-content/uploads/2019/09/Test_Coverage_Criteria_for_RESTful_Web_APIs.pdf)

## License

`OpenAPI specification coverage` is open-sourced software licensed under the [MIT](/LICENSE) License.