# VALUE OBJECTS (VOs)

Stock2Shop provides a number of Value Objects (abbr. VOs). 
Value Objects are part of the stock2shop/vo namespace.

A Value Object in Object-Oriented Programming must never:

- Access the database
- Query ElasticSearch or other external services.
- Define business logic
- Proxy to external systems
- Import or use other classes defined in this repository.

PHP classes that implement the functionality listed above must go in your Data Access Layer (DAL) in the 
/www/v1/stock2shop/dal/[your-channel] folder.

## INDEX

The table lists the available Value Object classes:

| Name              | Description                                                                                                                                                                                                                                                                                                                                                |
|-------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Channel           | Channels (or 'sales channels') are places where products are displayed and sold. <br/>You will use the Channel VO to represent these in the code. <br/>Stock2Shop essentially supports three types of channels: sales channels (your WooCommerce, Magento or Shopify site), marketplace channels (such as Takealot) and Business-To-Business/B2B channels. |
| ChannelImage      |                                                                                                                                                                                                                                                                                                                                                            |
| ChannelProduct    |                                                                                                                                                                                                                                                                                                                                                            | 
| ChannelProductGet |                                                                                                                                                                                                                                                                                                                                                            | 
| ChannelVariant    |                                                                                                                                                                                                                                                                                                                                                            |
| Flag              |                                                                                                                                                                                                                                                                                                                                                            | 
| Meta              |                                                                                                                                                                                                                                                                                                                                                            |
| Product           |                                                                                                                                                                                                                                                                                                                                                            |
| PriceTier         |                                                                                                                                                                                                                                                                                                                                                            |
| ProductOption     |                                                                                                                                                                                                                                                                                                                                                            |
| QtyAvailability   |                                                                                                                                                                                                                                                                                                                                                            |
| Variant           |                                                                                                                                                                                                                                                                                                                                                            |
