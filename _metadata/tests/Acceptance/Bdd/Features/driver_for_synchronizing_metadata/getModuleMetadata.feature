Feature: Get module metadata

  AS
  a visitor

  I WANT TO
  get the module's metadata from its ID code

  SO THAT
  I can see the whole module metadata


  @todo @domain
  Scenario: Can get an existing module with metadata

    Given there is the following metadata at metadata repository:
      | enableSync | category | tag | ModuleCode |
      | true  | FINANCE | foo,bar | 6a674a7f-76a6-42db-b0e3-b230a9587c93 |

    When I ask for getting the metadata for module with code "6a674a7f-76a6-42db-b0e3-b230a9587c93"

    Then I should obtain the following metadata:
      | enableSync | category | tag | ModuleCode |
      | true  | FINANCE | foo,bar | 6a674a7f-76a6-42db-b0e3-b230a9587c93 |


  @todo @domain
  Scenario: The module code doesn't exist

    Given there is no module with code "22136506-bf0b-4cf0-8e3d-3aad0c1eed67" at metadata repository

    When I ask for getting the ticket with code "22136506-bf0b-4cf0-8e3d-3aad0c1eed67"

    Then I should obtain no module
