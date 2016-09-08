Feature: Login
  In order to login
  As an anonymous user
  I need to be able to get a JWT

  Scenario: Login with valid credentials
    When I send a POST request to "/api/tokens" with values:
      | _email    | demodealex+1@gmail.com |
      | _password | lol                    |
    Then the response code should be 200
    And the response should contain "token"

  Scenario: Login with invalid email
    When I send a POST request to "/api/tokens" with values:
      | _email    | wrong@gmail.com |
      | _password | lol             |
    Then the response code should be 404

  Scenario: Login with invalid password
    When I send a POST request to "/api/tokens" with values:
      | _email    | demodealex+1@gmail.com |
      | _password | invalidpassword        |
    Then the response code should be 401

  Scenario: Login with valid credentials but with GET method
    When I send a GET request to "/api/tokens" with values:
      | _email    | demodealex+1@gmail.com |
      | _password | lol                    |
    Then the response code should be 405
