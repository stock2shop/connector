<?php

namespace stock2shop\dal\channels\example;

use stock2shop\vo;
use stock2shop\helpers;
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
     * This method returns the channel order data for items which match the
     * `channel_order_code` which have been set on the $orders array items.
     *
     * NB: The `channel_order_code` property is always set to the value of the
     * order ID which is used on your channel as a unique identifier for order
     * items. (i.e. WooCommerce's post ID).
     *
     * @param vo\ChannelOrder[] $orders
     * @param vo\Channel $channel
     * @return vo\ChannelOrder[]
     */
    public function getByCode(array $orders, vo\Channel $channel): array {

        // Get Orders From Channel.

        // The $orders parameter will contain vo\ChannelOrder objects with only
        // their `channel_order_code` set. We have to get the order data for each item from the
        // channel.

        /** @var vo\ChannelOrder[] $channelOrders */
        $channelOrders = [];

        // -----------------------------------------

        $orderItems = data\Helper::getJSONFiles("orders");

        foreach($orders as $order) {

            // Generate Prefix.

            // The orders are saved to the channel using their IDs in encoded
            // format as the identifier. In this example the encoded ID is the
            // prefix for the filename the order is saved as.
            $prefix = urlencode($order->system_order->channel_order_code);

            // -----------------------------------------

            // This gets the order data from the channel for the specific order.
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, "orders");

            // -----------------------------------------

            foreach($currentFiles as $fileName => $fileData) {

                if($fileName === $prefix . ".json") {

                    // Create VO.

                    // The order ID matches the prefix / filename of the order on the channel.
                    // Now we need to populate a `vo\ChannelOrder` object with the channel data.
                    $channelOrders[$prefix] = $this->transform($fileData, $channel);

                }

            }

        }

        return $channelOrders;

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
     * - Set the order number/reference code to the 'id' property.
     * - Set the customer details (name, email, etc) to `vo\SystemCustomer` object and
     *   set this to the 'customer' property of the `vo\ChannelOrderOrder`.
     * - Set the shipping and/or billing address data to the `vo\ChannelOrderOrder` object's
     *   address properties (see `vo\Order`).
     * - Set the item(s) - products, services rendered, etc - to `vo\OrderItem` item(s).
     * - Set each `vo\OrderItem` to the 'line_items' property of the `vo\Order` object.
     * - Implement functionality for any custom channel meta configured for the order transform.
     *
     * @param mixed $webhookOrder
     * @param vo\Channel $channel
     * @return vo\SystemOrder $systemOrder
     * @throws exceptions\UnprocessableEntity
     */
    public function transform($webhookOrder, vo\Channel $channel): vo\ChannelOrder {

        // Channel Meta.
        $meta = $channel->meta;

        // ---------------------------------------------------

        // Channel Order.

        // You will need to return a vo\ChannelOrder object (as per the method signature).
        // This is where you add the logic to transform the raw order data received from the
        // channel.

        $systemOrder = new vo\SystemOrder([
            "channel" => $channel
//            "system_order" => $channelOrderOrder
        ]);

        // ---------------------------------------------------

        // ChannelOrder Order

        // Inline a new vo\SystemCustomer object on the "customer" property.
        // The webhook order will always have customer data in it in some format or the other.
        // This must be assigned to an object of the vo\Customer class. Set the 'first_name' and
        // 'email' properties and any others for which there is data.

        // TODO: Confirm that using the Customer class' meta property to add in any additional fields
        //  with the Meta VO is acceptable.

        $customerMeta = [];
        $customerMeta[] = new vo\Meta([ "key" => "tel", "value" => $webhookOrder["customer"]["tel"]]);

        $channelOrderOrder = new vo\ChannelOrderOrder([
            "customer" => new vo\SystemCustomer([
                'first_name' => $webhookOrder['customer']['name'],
                'email' => $webhookOrder['customer']['email'],
                "meta" => $customerMeta
            ]),
            "channel_order_code" => $webhookOrder["order_number"],
        ]);

        // ---------------------------------------------------

        // Address(es).

        // In this example, there are two addresses for physical and postal locations which must
        // be mapped onto Stock2Shop Value Objects.

        // TODO: Confirm how the addresses are to be mapped. Onto the `vo\ChannelOrderOrder`
        //  as well as the customer addresses[] property?

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
        $systemOrder->system_order = $channelOrderOrder;

        // TODO: Update this as per discussion in standup 28/02/2022.
//        $channelOrder->system_order->id = $webhookOrder["order_no];
//        $channelOrder->system_order->channel_order_code = $webhookOrder["order_no];

        // Return populated channel order.
        return $systemOrder;

    }

}