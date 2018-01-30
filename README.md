# Billing Admin Service

## Purpose

The purpose of the Billing admin service is to generate the correct invoice data for each rate card, for the billing front end system. The service has a simple form which allows users to set off the generation of invoice data for specific rate cards linked to campaigns.

The primary use case for the service will be offering a platform for 'technical billing team' users to generate invoice data through the 'Billing Admin' interface entered via the hub page.

The service will require access to the front end database and CAPI for user authentication.

## Hosting
The service will need the following environments.
- QA at qa-billing-admin.flashtalking.net
- Live as billing-admin.flashtalking.net

The deployment pipeline should run tests on push, then pdeploy to QA. Initially QA to Live will be a manual deployment process once the changes have been thoroughly tested. The QA and Live applications will only be available to flashtalking internal users who have access to the current billing admin system.

### Environment Variables

- BILLINGADMINSERVICE_ENV - (qa|prod|dev|testing) defaults to DEV, determines config file config/$BILLINGADMINSERVICE_ENV.php that will be loaded

Read and write hosts are separated for easier testing with live replica data.  For production they'd likely be the same.

- WRITEHOST
- WRITEDB
- WRITEUSERNAME
- WRITEPASSWORD

- READHOST
- READDB
- READUSERNAME
- READPASSWORD

Summary data (likely from querybuilder)

- SUMMARYHOST
- SUMMARYDB
- SUMMARYUSERNAME
- SUMMARYPASSWORD

## Data Model
### Entity/Table Mapping

FLASHTALKING SCHEMA

| Entity Name         | Table Name                   | Access |
|---------------------|------------------------------|--------|
| Creatives           | tblcreatives                 | READ   |
| CustomReportType    | tblcreativecustomreporttype  | READ   |
| Total Creative Size | tbltotalcreativesize         | READ   |
| Version Package     | version_package_history      | READ   |

PS_XRE SCHEMA

| Entity Name                | Table Name                 | Access |
|----------------------------|----------------------------|--------|
| Business entity            | business_entity            | READ   |
| Business entity group      | business_entity_group      | READ   |
| Business entity group Link | business_entity_group_link | READ   |
| Campaign                   | campaign                   | READ   |
| Campaign external          | campaign_ext               | READ   |
| Campaign optional attribute| campaign_optional_attribute| READ   |
| Optional Attribute         | optional_attribute         | READ   |
| Placement                  | placement                  | READ   |
| Placement package          | placement_package          | READ   |
| Site                       | site                       | READ   |
| Site Dept Owner            | site_dept_owner            | READ   |
| Package                    | package                    | READ   |
| Spotlight groups           | spotlight_group            | READ   |
| Placement smartclip        | placement_smartclip        | READ   |

FT_BILLING SCHEMA

| Entity Name                              | Table Name                  | Access                      |
|------------------------------------------|-----------------------------|-----------------------------|
| Activity invoice                         | activity_invoice            | READ/WRITE                  |
| Campaign charges                         | campaign_charge             | READ                        |
| Custom Billing Dates                     | custom_billing_dates        | READ                        |
| Invoice                                  | invoice                     | READ/WRITE                  |
| Invoice campaign                         | invoice_campaign            | READ/WRITE/DELETE           |
| Invoice Template                         | invoice_template            | READ                        |
| Invoice Template RC                      | invoice_template_rc         | READ                        |
| Invoice totals                           | invoice_total               | READ/WRITE                  |
| Invoice extra                            | invoice_extra               | WRITE                       |
| Insertion order                          | io                          | READ                        |
| insertion order item                     | io_item                     | READ                        |
| Latest activity rate card                | latest_activity_rate_card_2 | READ (NOTE, this is a view) |
| Latest activity invoice                  | latest_activity_invoice_2   | READ (NOTE, this is a view) |
| Live rate cards                          | live_rate_cards             | READ (NOTE, this is a view) |
| MediaBuyer not billed (used for testing) | mediaBuyer_notBilled        | READ                        |
| Rate card                                | rate_card                   | READ/WRITE                  |
| Rate card item                           | rate_card_item              | READ/WRITE                  |
| Rate card item fields                    | rate_card_item_fields       | READ                        |
| Rate card item values                    | rate_card_item_values       | READ                        |
| UI activity                              | ui_activity                 | READ                        |

SHA_BUSINESS SCHEMA

| Entity Name         | Table Name          | Access |
|---------------------|---------------------|--------|
| Ad Index            | ad_index            | READ   |
| Campaign            | campaign            | READ   |
| Creative            | creative            | READ   |
| Placement           | placement           | READ   |
| Placement smartclip | placement_smartclip | READ   |
| Spotlight           | spotlight           | READ   |

SHA_SUMMARY SCHEMA

| Entity Name           | Table Name                    | Access |
|-----------------------|------------                   |--------|
| Click Summary         | click_summary                 | READ   |
| Decision Tree Summary | decision_tree_summary         | READ   |
| Impression Summary    | impression_summary            | READ   |
| Spotlight Summary     | spotlight_summary             | READ   |
| Mobile views log      | mrc_viewability_summary log   | READ   |

There will be additional data transfer between the reporting system and this service to be defined at a later date.

No additional schema changes will be required.

## Dependencies
- Generated invoice data is used by the billing invoice system. The current system also has a number of requests to external services (Billing front end/Creative Interface) but these will be refactored to run internally.

## External Services
- The scripts currently use security/config information from WFE (auto_cred/db_details/index_security) but these will be moved to within the service config.

## Security
- Authentication is done via a CAPI Bearer Token in the Authorization header of each request. The service hasn't used CAPI previously so will need a CAPI account/authorisation setting up.

## Deployment
- As this service requires access to the WFE database it needs to be hosted either in the datacenters or on the AWS VPN'd account

## API Docs
- Swagger API docs will be available on the /docs endpoint.

## Local Development

### To bring up the local environment -

- Download the git repo and navigate to the correct folder
- 'vagrant up' to bring up the dev env
- 'sudo composer install' to add the correct composer repo.
- 'vagrant ssh' to get onto the devbox
- 'cd /vagrant' to go to root of site
- 'make docker-build-dev' to build the devenv
- 'make run' to run up the devenv
- add the following to your hosts file

      192.168.33.10 billing-admin-service.local

- Navigate to the billing admin service status page at

      http://billing-admin-service.local/status

## Validation

### Invoice page campaign validation
- python script to identify differences between the campaigns listed for each rate card:

      test/validate/main.py

- install python3 and pip on host

      $ curl https://bootstrap.pypa.io/get-pip.py | python3

- install package beautifulsoup4

      $ pip install beautifulsoup4

- run the validation script

      python3 test/validation/main.py
