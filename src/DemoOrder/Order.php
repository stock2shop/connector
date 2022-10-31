<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoOrder;

use Stock2Shop\Share;

class Order extends Base
{
    public Address $billing_address;
    /** @var Customer[] $customer */
    public array $customer;
    /** @var LineItem[] $line_items */
    public array $line_items;
    public Payment $payment;
    public Address $shipping_address;
    public Visitor $visitor;
    public ?string $payment_additional_info;
    public ?string $payu_additional_info;
    public ?string $payu_payment_status;
    public ?string $payu_payment_date;
    public ?string $entity_id;
    public ?string $state;
    public ?string $status;
    public ?string $protect_code;
    public ?string $shipping_description;
    public ?string $is_virtual;
    public ?string $store_id;
    public ?string $base_discount_amount;
    public ?int $base_discount_invoiced;
    public ?string $base_grand_total;
    public ?string $base_shipping_amount;
    public ?float $base_shipping_invoiced;
    public ?string $base_shipping_tax_amount;
    public ?string $base_subtotal;
    public ?float $base_subtotal_invoiced;
    public ?string $base_tax_amount;
    public ?float $base_tax_invoiced;
    public ?string $base_to_global_rate;
    public ?string $base_to_order_rate;
    public ?float $base_total_invoiced;
    public ?float $base_total_invoiced_cost;
    public ?float $base_total_paid;
    public ?string $discount_amount;
    public ?float $discount_invoiced;
    public ?string $grand_total;
    public ?string $shipping_amount;
    public ?float $shipping_invoiced;
    public ?string $shipping_tax_amount;
    public ?string $store_to_base_rate;
    public ?string $store_to_order_rate;
    public ?string $subtotal;
    public ?float $subtotal_invoiced;
    public ?string $tax_amount;
    public ?float $tax_invoiced;
    public ?float $total_invoiced;
    public ?float $total_paid;
    public ?string $total_qty_ordered;
    public ?string $customer_is_guest;
    public ?string $customer_note_notify;
    public ?string $billing_address_id;
    public ?string $customer_group_id;
    public ?string $quote_id;
    public ?string $shipping_address_id;
    public ?string $base_shipping_discount_amount;
    public ?string $base_subtotal_incl_tax;
    public ?string $base_total_due;
    public ?string $shipping_discount_amount;
    public ?string $subtotal_incl_tax;
    public ?string $total_due;
    public ?string $weight;
    public ?string $increment_id;
    public ?string $base_currency_code;
    public ?string $customer_email;
    public ?string $customer_firstname;
    public ?string $customer_lastname;
    public ?string $global_currency_code;
    public ?string $order_currency_code;
    public ?string $remote_ip;
    public ?string $shipping_method;
    public ?string $store_currency_code;
    public ?string $store_name;
    public ?string $x_forwarded_for;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $total_item_count;
    public ?string $discount_tax_compensation_amount;
    public ?string $base_discount_tax_compensation_amount;
    public ?string $shipping_discount_tax_compensation_amount;
    public ?string $base_shipping_discount_tax_compensation_amnt;
    public ?float $discount_tax_compensation_invoiced;
    public ?float $base_discount_tax_compensation_invoiced;
    public ?string $shipping_incl_tax;
    public ?string $base_shipping_incl_tax;
    public ?string $paypal_ipn_customer_notified;
    public ?string $bpid;
    public ?float $shipping_tax_invoiced;
    public ?float $base_shipping_tax_invoiced;
    public ?string $payment_additional_information;


    public function __construct(array $data)
    {
        $this->billing_address                              = new Address(self::arrayFrom($data, 'billing_address'));
        $this->customer                                     = Customer::createArray(self::arrayFrom($data, 'customer'));
        $this->line_items                                   = LineItem::createArray(self::arrayFrom($data, 'line_items'));
        $this->payment                                      = new Payment(self::arrayFrom($data, 'payment'));
        $this->shipping_address                             = new Address(self::arrayFrom($data, 'shipping_address'));
        $this->shipping_address                             = new Address(self::arrayFrom($data, 'shipping_address'));
        $this->visitor                                      = new Visitor(self::arrayFrom($data, 'visitor'));
        $this->payment_additional_info                      = self::stringFrom($data, 'payment_additional_info');
        $this->payu_additional_info                         = self::stringFrom($data, 'payu_additional_info');
        $this->payu_payment_status                          = self::stringFrom($data, 'payu_payment_status');
        $this->payu_payment_date                            = self::stringFrom($data, 'payu_payment_date');
        $this->entity_id                                    = self::stringFrom($data, 'entity_id');
        $this->state                                        = self::stringFrom($data, 'state');
        $this->status                                       = self::stringFrom($data, 'status');
        $this->protect_code                                 = self::stringFrom($data, 'protect_code');
        $this->shipping_description                         = self::stringFrom($data, 'shipping_description');
        $this->is_virtual                                   = self::stringFrom($data, 'is_virtual');
        $this->store_id                                     = self::stringFrom($data, 'store_id');
        $this->base_discount_amount                         = self::stringFrom($data, 'base_discount_amount');
        $this->base_discount_invoiced                       = self::intFrom($data, 'base_discount_invoiced');
        $this->base_grand_total                             = self::stringFrom($data, 'base_grand_total');
        $this->base_shipping_amount                         = self::stringFrom($data, 'base_shipping_amount');
        $this->base_shipping_invoiced                       = self::floatFrom($data, 'base_shipping_invoiced');
        $this->base_shipping_tax_amount                     = self::stringFrom($data, 'base_shipping_tax_amount');
        $this->base_subtotal                                = self::stringFrom($data, 'base_subtotal');
        $this->base_subtotal_invoiced                       = self::floatFrom($data, 'base_subtotal_invoiced');
        $this->base_tax_amount                              = self::stringFrom($data, 'base_tax_amount');
        $this->base_tax_invoiced                            = self::floatFrom($data, 'base_tax_invoiced');
        $this->base_to_global_rate                          = self::stringFrom($data, 'base_to_global_rate');
        $this->base_to_order_rate                           = self::stringFrom($data, 'base_to_order_rate');
        $this->base_total_invoiced                          = self::floatFrom($data, 'base_total_invoiced');
        $this->base_total_invoiced_cost                     = self::floatFrom($data, 'base_total_invoiced_cost');
        $this->base_total_paid                              = self::floatFrom($data, 'base_total_paid');
        $this->discount_amount                              = self::stringFrom($data, 'discount_amount');
        $this->discount_invoiced                            = self::floatFrom($data, 'discount_invoiced');
        $this->grand_total                                  = self::stringFrom($data, 'grand_total');
        $this->shipping_amount                              = self::stringFrom($data, 'shipping_amount');
        $this->shipping_invoiced                            = self::floatFrom($data, 'shipping_invoiced');
        $this->shipping_tax_amount                          = self::stringFrom($data, 'shipping_tax_amount');
        $this->store_to_base_rate                           = self::stringFrom($data, 'store_to_base_rate');
        $this->store_to_order_rate                          = self::stringFrom($data, 'store_to_order_rate');
        $this->subtotal                                     = self::stringFrom($data, 'subtotal');
        $this->subtotal_invoiced                            = self::floatFrom($data, 'subtotal_invoiced');
        $this->tax_amount                                   = self::stringFrom($data, 'tax_amount');
        $this->tax_invoiced                                 = self::floatFrom($data, 'tax_invoiced');
        $this->total_invoiced                               = self::floatFrom($data, 'total_invoiced');
        $this->total_paid                                   = self::floatFrom($data, 'total_paid');
        $this->total_qty_ordered                            = self::stringFrom($data, 'total_qty_ordered');
        $this->customer_is_guest                            = self::stringFrom($data, 'customer_is_guest');
        $this->customer_note_notify                         = self::stringFrom($data, 'customer_note_notify');
        $this->billing_address_id                           = self::stringFrom($data, 'billing_address_id');
        $this->customer_group_id                            = self::stringFrom($data, 'customer_group_id');
        $this->quote_id                                     = self::stringFrom($data, 'quote_id');
        $this->shipping_address_id                          = self::stringFrom($data, 'shipping_address_id');
        $this->base_shipping_discount_amount                = self::stringFrom($data, 'base_shipping_discount_amount');
        $this->base_subtotal_incl_tax                       = self::stringFrom($data, 'base_subtotal_incl_tax');
        $this->base_total_due                               = self::stringFrom($data, 'base_total_due');
        $this->shipping_discount_amount                     = self::stringFrom($data, 'shipping_discount_amount');
        $this->subtotal_incl_tax                            = self::stringFrom($data, 'subtotal_incl_tax');
        $this->total_due                                    = self::stringFrom($data, 'total_due');
        $this->weight                                       = self::stringFrom($data, 'weight');
        $this->increment_id                                 = self::stringFrom($data, 'increment_id');
        $this->base_currency_code                           = self::stringFrom($data, 'base_currency_code');
        $this->customer_email                               = self::stringFrom($data, 'customer_email');
        $this->customer_firstname                           = self::stringFrom($data, 'customer_firstname');
        $this->customer_lastname                            = self::stringFrom($data, 'customer_lastname');
        $this->global_currency_code                         = self::stringFrom($data, 'global_currency_code');
        $this->order_currency_code                          = self::stringFrom($data, 'order_currency_code');
        $this->remote_ip                                    = self::stringFrom($data, 'remote_ip');
        $this->shipping_method                              = self::stringFrom($data, 'shipping_method');
        $this->store_currency_code                          = self::stringFrom($data, 'store_currency_code');
        $this->store_name                                   = self::stringFrom($data, 'store_name');
        $this->x_forwarded_for                              = self::stringFrom($data, 'x_forwarded_for');
        $this->created_at                                   = self::stringFrom($data, 'created_at');
        $this->updated_at                                   = self::stringFrom($data, 'updated_at');
        $this->total_item_count                             = self::stringFrom($data, 'total_item_count');
        $this->discount_tax_compensation_amount             = self::stringFrom($data, 'discount_tax_compensation_amount');
        $this->base_discount_tax_compensation_amount        = self::stringFrom($data, 'base_discount_tax_compensation_amount');
        $this->shipping_discount_tax_compensation_amount    = self::stringFrom($data, 'shipping_discount_tax_compensation_amount');
        $this->base_shipping_discount_tax_compensation_amnt = self::stringFrom($data, 'base_shipping_discount_tax_compensation_amnt');
        $this->discount_tax_compensation_invoiced           = self::floatFrom($data, 'discount_tax_compensation_invoiced');
        $this->base_discount_tax_compensation_invoiced      = self::floatFrom($data, 'base_discount_tax_compensation_invoiced');
        $this->shipping_incl_tax                            = self::stringFrom($data, 'shipping_incl_tax');
        $this->base_shipping_incl_tax                       = self::stringFrom($data, 'base_shipping_incl_tax');
        $this->paypal_ipn_customer_notified                 = self::stringFrom($data, 'paypal_ipn_customer_notified');
        $this->bpid                                         = self::stringFrom($data, 'bpid');
        $this->shipping_tax_invoiced                        = self::floatFrom($data, 'shipping_tax_invoiced');
        $this->base_shipping_tax_invoiced                   = self::floatFrom($data, 'base_shipping_tax_invoiced');
        $this->payment_additional_information               = self::stringFrom($data, 'payment_additional_information');
    }

    /**
     * @return Order[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new Order((array)$item);
        }
        return $a;
    }
}
