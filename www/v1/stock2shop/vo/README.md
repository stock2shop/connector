# VALUE OBJECTS (VOs)

Stock2Shop provides a number of Value Objects (abbr. VOs). 
Value Objects are part of the stock2shop/vo namespace.

A Value Object in Object Oriented Programming must never:

- Access the database
- Query ElasticSearch or other external services.
- Define business logic
- Proxy to external systems
- Import or use other classes defined in this repository.

PHP classes that implement the functionality listed above must go in your 
Data Access Layer (DAL) in the /www/v1/stock2shop/dal/[your-channel] folder.

## INDEX

The table lists the available Value Object classes:

| Name                 | Description |
|----------------------|-------------|
| Address              |             |
| ChannelImage         |             |
| ChannelProduct       |             | 
| ChannelVariant       |             |
| Flag                 |             | 
| Meta                 |             |
| PriceTier            |             |
| Product              |             |
| ProductMetaDelete    |             |
| ProductOption        |             |
| QtyAvailability      |             |
| Segment              |             | 
| Source               |             |
| SourceProduct        |             |
| SourceProductProduct |             |
| SourceProductSource  |             |
| SystemCustomer       |             |
| SystemImage          |             |
| SystemProduct        |             |
| SystemProductProduct |             |
| SystemProductSource  |             |
| SystemCustomer       |             |
| SystemImage          |             |
| SystemProduct        |             |
| SystemSegment        |             |
| SystemVariant        |             |
| User                 |             |
| Variant              |             |

## CHANGE LOG