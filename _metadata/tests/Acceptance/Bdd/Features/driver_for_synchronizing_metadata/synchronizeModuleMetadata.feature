Feature: Synchronize module metadata

  AS
  an administrator

  I WANT TO
  update the module's metadata from its ID code

  SO THAT
  The local metadata and the source of metadata are equals


  @domain
  Scenario: Can update an existing module with new metadata from original source

    Given there is the following metadata at metadata repository:
      | enableSync | category | tag | moduleCode |
      | true  | PAYMENTS | payment,claim | 4868cd6a-b854-461e-91a4-fb30ad1ce2cd |

    Given there is the following metadata at metadata original source:
      | enableSync | category | tag |
      | true  | FINANCE | finance |

    When I ask for update the metadata for module with code "4868cd6a-b854-461e-91a4-fb30ad1ce2cd"

    Then I should obtain the following metadata:
      | enableSync | category | tag | moduleCode |
      | true  | FINANCE | finance | 6a674a7f-76a6-42db-b0e3-b230a9587c93 |


