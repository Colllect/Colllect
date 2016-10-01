Feature: Get an user
  In order to delete an user account
  As an anonymous user
  I need to be able to delete user

  Scenario: Delete an existing user
    When I send a DELETE request to "/api/users/2"
    Then the response code should be 204

  Scenario: Delete a non-existing user
    When I send a DELETE request to "/api/users/100"
    Then the response code should be 404

  Scenario: Get a deleted user data
    Given I send a DELETE request to "/api/users/2"
    When I send a GET request to "/api/users/2"
    Then the response code should be 404
