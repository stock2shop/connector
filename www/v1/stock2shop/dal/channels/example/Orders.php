<?php

namespace stock2shop\dal\channels\example;

use stock2shop\vo;
use stock2shop\exceptions;
use stock2shop\dal\channel\Orders as OrdersInterface;

/**
 * Orders
 *
 * @package stock2shop\dal\example
 */
class Orders implements OrdersInterface
{

    /**
     * Get
     *
     * This method returns the orders which have been synchronised to the channel.
     *
     * @param string $token
     * @param int $limit
     * @param vo\Channel $channel
     * @return array
     */
    public function get(string $token, int $limit, vo\Channel $channel): array {

        // Iterator position.
        $cnt = 1;

        // ---------------------------------------------------

        // Get Order Data.

        // This example uses a flat-file data source (local disk) to represent the
        // storage location of the order data. (similarly to the implementation in
        // `example\Products`). A 'channel order file' is akin to a raw order posted
        // by your system's webhook mechanism.

        $channelOrderFiles = data\Helper::getJSONFiles('orders');

        // ---------------------------------------------------

        // Array to hold the orders.
        $channelOrders = [];

        // Iterate over the order files.
        foreach ($channelOrderFiles as $fileName => $rawOrderFile) {

            // ---------------------------------------------------

            // Filter By Token.

            // Here we do a rudimentary logic check to establish whether the
            // order ID matches what is required by the $token parameter.

            // ---------------------------------------------------

            if (strcmp($token, $fileName) < 0) {

                // This is where we break out of the loop and return the orders if
                // we have reached the required '$limit'.
                if ($cnt > $limit) {
                    break;
                }

                // ---------------------------------------------------

                // Transform Order.

                // The method now passes the '$order' to the transform method which
                // will do the mapping required to turn a webhook into a Stock2Shop
                // compliant `vo\ChannelOrder` object. The `vo\Channel` object is also
                // passed to the transform() method because we may need the channel's
                // metadata information in order to do the transformation.
                $channelOrders[] = $this->transform($rawOrderFile, $channel);

                // ---------------------------------------------------

                // Increment counter.
                $cnt++;

            }

        }

        // Return channel orders.
        return $channelOrders;

    }

    /**
     * Get Orders By Code
     *
     * This method returns orders from the channel.
     * It is used in the
     *
     * @param vo\ChannelOrder[] $orders
     * @param vo\Channel $channel
     * @return vo\ChannelOrder[]
     */
    public function getByCode(array $orders, vo\Channel $channel): array {

        return [];

    }

    /**
     * Transform Order.
     *
     * Transform should convert the order "webhook" sent by the
     * channel into a vo\ChannelOrder and return it. This is where
     * you will add custom logic to map the fields of the webhook onto
     * our Stock2Shop specifications.
     *
     * Please refer carefully to the `vo\ChannelOrder` class in the vo
     * directory of this repository for the required fields and how
     * to use the object.
     *
     * Notes/integration guidelines:
     *
     * - Set the order notes/comments/instructions/details to the 'notes' property.
     * - Set the order number/reference code to the 'channel_order_code' property.
     * - Set the customer details (name, email, etc) to `vo\SystemCustomer` object and
     *   set this to the 'customer' property of the `vo\ChannelOrderOrder`.
     * - Set the item(s) - products, services rendered, etc - to `vo\OrderItem` item(s).
     * - Set each `vo\OrderItem` to the 'line_items' property of the `vo\Order` object.
     * - Implement functionality for any custom channel meta configured for the order transform.
     *
     * @param mixed $webhookOrder
     * @param vo\Channel $channel
     * @return vo\ChannelOrder
     */
    public function transform($webhookOrder, vo\Channel $channel): vo\ChannelOrder {

        // Get meta from channel.
        $meta = $channel->meta;

        // ---------------------------------------------------

        // Channel Order.

        // You will need to return a vo\ChannelOrder object (as per the method signature).
        // This is where you add the logic to transform the raw order data received from the channel.
        // The way this is done doesn't matter, although it is always better to segregate code
        // sensibly.

        // The system_order has been left out below because it is only added to the structure at the
        // end of the workflow:
        $channelOrder = new vo\ChannelOrder([
            "channel" => $channel
//            "system_order" => $channelOrderOrder
        ]);

        // ---------------------------------------------------

        // ChannelOrder Order

        // Inline a new vo\SystemCustomer object on the "customer" property. Create
        // vo\ChannelOrderOrder. The webhook order will always have customer data in it in some
        // format or the other. This must be assigned to an object of the vo\Customer class. Set
        // the 'first_name' and 'email' properties and any others for which there is data.

        $channelOrderOrder = new vo\ChannelOrderOrder([
            "customer" => new vo\SystemCustomer([
                'first_name' => $webhookOrder['customer']['name'],
                'email' => $webhookOrder['customer']['email'],
            ]),
            "channel_order_code" => $webhookOrder["order_number"]
        ]);

        // ---------------------------------------------------

        // Address(es).

        // In this example, there are two addresses for physical and postal locations which must
        // be mapped onto Stock2Shop Value Objects. The process follows:

        // - postal_address
        $channelOrderOrder->customer->addresses[] = new vo\Address([
            'address1' => $webhookOrder['postal_address']['street'],
            'country' => $webhookOrder['postal_address']['country'],
            'zip' => $webhookOrder['postal_address']['zip'],
        ]);

        // - delivery_address
        $channelOrderOrder->customer->addresses[] = new vo\Address([
            'address1' => $webhookOrder['delivery_address']['street'],
            'country' => $webhookOrder['delivery_address']['country'],
            'zip' => $webhookOrder['delivery_address']['zip'],
        ]);

        // ---------------------------------------------------

        // Order Item(s).

        // If the webhook is a valid order, then there will be order line items which each
        // represent a product that's been sold. Remember that Stock2Shop defines products which
        // are sold/shipped as 'Product Variants' or 'Variants' and that a 'product' refers to a
        // collection of different variations in product.

        // Iterate over the items in the webhook structure.
        foreach ($webhookOrder['items'] as $item) {

            // TODO: Ask Chris if the calculations should be done here as well.

            // The vo\ChannelOrder class definition includes a 'line_items' array property which
            // you must use to populate the order with sold products. Please note that the line item's
            // 'channel_variant_code' property is being set to the webhook order's sku value (this
            // could also be the product ID on the system you are integrating with). In Stock2Shop,
            // `vo\OrderItem` objects are the same as product variants.
            $channelOrderOrder->line_items[] = new vo\OrderItem([
                'sku' => $item['sku'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'source_variant_code' => $item['sku']
            ]);

        }

        // ---------------------------------------------------

        // Attach `vo\ChannelOrderOrder` to `vo\ChannelOrder` object.
        $channelOrder->system_order = $channelOrderOrder;

        // Return populated channel order.
        return $channelOrder;

    }

}