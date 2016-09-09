Feature: User registration
  In order to register
  As an anonymous user
  I need to be able to create an account

  Scenario: Create an account with valid form
    When I send a POST request to "/api/users" with values:
      | email    | demodealex+register@gmail.com |
      | password | lol                           |
      | nickname | My super nickname             |
    Then print response
    Then the response code should be 201
    And the response should contain "id"
    And the response should contain "email"
    And the response should contain "nickname"
    And the response should not contain "password"
