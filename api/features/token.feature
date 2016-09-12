Feature: Get a JWT
  In order to login
  As an anonymous user
  I need to be able to get a JWT

  Scenario: Get JWT with valid credentials
    When I send a POST request to "/api/tokens" with values:
      | email    | demodealex+1@gmail.com |
      | password | 12345678               |
    Then the response code should be 201
    And the response should contain "token"

  Scenario: Get JWT with invalid email
    When I send a POST request to "/api/tokens" with values:
      | email    | wrong@gmail.com |
      | password | 12345678        |
    Then the response code should be 404

  Scenario: Get JWT with invalid password
    When I send a POST request to "/api/tokens" with values:
      | email    | demodealex+1@gmail.com |
      | password | invalidpassword        |
    Then the response code should be 401

  Scenario: Get JWT with valid credentials but with GET method
    When I send a GET request to "/api/tokens" with values:
      | email    | demodealex+1@gmail.com |
      | password | 12345678               |
    Then the response code should be 405
