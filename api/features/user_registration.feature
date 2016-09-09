Feature: User registration
  In order to register
  As an anonymous user
  I need to be able to create an account

  Scenario: Create an account with valid form
    When I send a POST request to "/api/users" with values:
      | email    | demodealex+register@gmail.com |
      | password | lol                           |
      | nickname | My super nickname             |
    Then the response code should be 201
    And the response should not contain "password"
    And the response should contain "created_at"
    And the response should contain json:
      """
      {
        "id": 11,
        "email": "demodealex+register@gmail.com",
        "nickname": "My super nickname",
        "roles": ["ROLE_USER"]
      }
      """

  Scenario: Create an account with already used email
    When I send a POST request to "/api/users" with values:
      | email    | demodealex+1@gmail.com        |
      | password | lol                           |
      | nickname | My super nickname             |
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

  Scenario: Create an account with blank values
    When I send a POST request to "/api/users" with values:
      | email    |    |
      | password |    |
      | nickname |    |
    Then the response code should be 400
    And the response should contain json:
      """
      {
        "code": 400,
        "errors": {
          "children": {
            "email": {
              "errors": ["cannot_be_blank"]
            },
            "nickname": {
              "errors": ["cannot_be_blank"]
            },
            "plainPassword": {
              "errors": ["cannot_be_blank"]
            }
          }
        }
      }
      """
