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
      | enableSync | category | tag | moduleCode | moduleRepositoryUrl |
      | true  | PAYMENTS | payment,claim | 4868cd6a-b854-461e-91a4-fb30ad1ce2cd | https://www.github.com/foo/bar |

    Given there is the following metadata at metadata original source "https://www.github.com/foo/bar"
      | enableSync | category | tag |
      | true  | FINANCE | finance, payment |

    When I ask for update the metadata for module with code "4868cd6a-b854-461e-91a4-fb30ad1ce2cd"

    Then I should obtain the following updated metadata:
      | enableSync | category | tag | moduleCode |
      | true  | FINANCE | finance, payment | 4868cd6a-b854-461e-91a4-fb30ad1ce2cd |


