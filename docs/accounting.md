# Account Database Design

## Accounts Table

### Fields
<!-- table -->
| Field | Type | Description |
| --- | --- | --- |
| id | string | Primary key (uuid) |
| number | string | Account number |
| name | string | Account name |
| type | string | Account type |
| balance | number | Account balance |
| currency | string | Account currency |
| created_at | date | Account creation date |
| updated_at | date | Account last update date |
| deleted_at | date | Account deletion date |
<!-- /table -->

### Relationships

<br>

<!-- table -->
| Relationship | Type | Description |
| --- | --- | --- |
| transactions | hasMany | Account transactions |
<!-- /table -->

<br>

----
<br>

## Transactions Table

### Fields

<!-- table -->
| Field | Type | Description |
| --- | --- | --- |
| id | string | Primary key (uuid) |
| account_id | string | Account id |
| amount | number | Transaction amount |
| currency | string | Transaction currency |
| created_at | date | Transaction creation date |
| updated_at | date | Transaction last update date |
| deleted_at | date | Transaction deletion date |
<!-- /table -->

<br>

### Relationships

<!-- table -->
| Relationship | Type | Description |
| --- | --- | --- |
| account | belongsTo | Transaction account |
<!-- /table -->