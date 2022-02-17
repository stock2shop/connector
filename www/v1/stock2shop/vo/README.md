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
`/www/v1/stock2shop/dal/[your-channel]` folder.

## INDEX

The table lists the available Value Object classes:

| Name              | Description                                                                                                                                                                                                                                                                                                                                                                               |
|-------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Channel           | Channels (or 'sales channels') are places where products are displayed and sold. <br/>You will use the Channel VO to represent these in the code. <br/>Stock2Shop essentially supports three types of channels: sales channels (your WooCommerce, Magento or Shopify site), marketplace channels (such as Takealot) and Business-To-Business/B2B channels.                                |
| ChannelImage      | Channel Images are represented using this VO. <br/>use the valid() class method to check the objects.                                                                                                                                                                                                                                                                                     |
| ChannelProduct    | Use this VO to represent the products you are going to synchronize from the source system (i.e. WooCommerce) onto a Stock2Shop Channel. <br/>The meta property must be populated with an array of Meta VO objects. <br/>The images property must be populated with an array of ChannelImage objects.<br/>The variants property must be populated with an array of ChannelVariant objects. | 
| ChannelProductGet | Use this VO to represent ChannelProducts along with a cursor token. <br/>Used in the get() method in the Products class.                                                                                                                                                                                                                                                                  | 
| ChannelVariant    | ChannelProduct variants describe the actual products to be synced onto a Stock2Shop channel. A ChannelVariant is associated with one ChannelProduct object.                                                                                                                                                                                                                               |
| Flag              | Flags are used to configure which values which may be overwritten in a Channel.                                                                                                                                                                                                                                                                                                           | 
| Meta              | Meta VOs are used to describe channels, sources, orders and more. <br/>You must use meta to structure attributes and information about the products you are going to synchronise to one of our channels.                                                                                                                                                                                  |
| Product           | This is the base class for all products in the Stock2Shop system. <br/>Extended by the ChannelProduct VO.                                                                                                                                                                                                                                                                                 |
| PriceTier         | This VO is used to represent prices in relation to a pricing "tier".                                                                                                                                                                                                                                                                                                                      |
| ProductOption     | Use this VO to represent options available for a Product.                                                                                                                                                                                                                                                                                                                                 |
| QtyAvailability   | This VO is used to represent availability of an entity in the Stock2Shop system. <br/>Used in the Variant base class.                                                                                                                                                                                                                                                                     |
| Variant           | This is the base class in our system used for representing variants. <br/>Used by ChannelVariant.                                                                                                                                                                                                                                                                                         |
