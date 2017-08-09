Feature: User update
  In order to update user data
  As an anonymous user
  I need to be able to update an account data

  Scenario: Update an account with valid form
    When I send a PUT request to "/api/users/1" with values:
      | email         | demodealex+updated@gmail.com |
      | plainPassword | 87654321                     |
      | nickname      | My new nickname              |
    Then the response code should be 200
    And the response should not contain "password"
    And the response should not contain "plainPassword"
    And the response should contain "created_at"
    And the response should contain json:
      """
      {
        "id": 1,
        "email": "demodealex+updated@gmail.com",
        "nickname": "My new nickname",
        "roles": ["ROLE_USER"]
      }
      """

  Scenario: Update an account with already used email
    When I send a PUT request to "/api/users/1" with values:
      | email         | demodealex+3@gmail.com |
    Then the response code should be 400
    And the response should contain json:
      """
      {
        "code": 400,
        "errors": {
          "children": {
            "email": {
              "errors": ["already_used"]
            },
            "nickname": [],
            "plainPassword": []
          }
        }
      }
      """

  Scenario: Only update the email on an account
    When I send a PUT request to "/api/users/1" with values:
      | email         | demodealex+emailonly@gmail.com |
    Then the response code should be 200
    And the response should not contain "password"
    And the response should not contain "plainPassword"
    And the response should contain "nickname"
    And the response should contain "created_at"
    And the response should contain json:
      """
      {
        "id": 1,
        "email": "demodealex+emailonly@gmail.com",
        "roles": ["ROLE_USER"]
      }
      """
