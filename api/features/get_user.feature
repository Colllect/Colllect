Feature: Get an user
  In order to get user data
  As an anonymous user
  I need to be able to access to user data

  Scenario: Get existing user data
    When I send a GET request to "/api/users/1"
    Then the response code should be 200
    And the response should not contain "password"
    And the response should contain "nickname"
    And the response should contain "created_at"
    And the response should contain json:
      """
      {
        "id": 1,
        "email": "demodealex+1@gmail.com",
        "roles": ["ROLE_USER"]
      }
      """

  Scenario: Get non-existing user data
    When I send a GET request to "/api/users/100"
    Then the response code should be 404
