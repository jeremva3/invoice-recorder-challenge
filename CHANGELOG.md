# Changelog

## [Unreleased]

### Added
- **Registration of series, number, type of the voucher, and currency**
  - Created a migration file to add the missing fields to the vouchers table.
  - Modified the endpoint `/api/v1/vouchers/` to save series, number, type, and currency information for new vouchers.
  - Created a new endpoint to regularize existing records by extracting data from the `xml_content` field.

- **Total amounts endpoint**
  - Added a new endpoint `/api/v1/vouchers/total-amounts` to retrieve total accumulated amounts in soles and dollars.

- **Voucher deletion**
  - Created a new endpoint `/api/v1/vouchers/{id}` to delete vouchers by ID, including their respective details.

- **Filtering in voucher listings**
  - Updated the existing endpoint `/api/v1/vouchers/` to allow filtering by series, number, start date, and end date.
