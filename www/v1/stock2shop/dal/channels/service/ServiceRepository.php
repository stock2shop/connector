<?php

namespace stock2shop\dal\channels\service;

/**
 * Service Repository
 *
 * Stores the state for the channel in memory as an example
 * i.e. products, orders ad fulfillments. This class is a
 * Singleton which means only one instance/object of this
 * class may exist in the application.
 *
 * @package stock2shop\dal\service
 */
class ServiceRepository
{
    /**
     * @var private static $instance
     */
    private static $instance = null;

    /**
     * @var service\ServiceProduct[]
     */
    private static $products = [];

    /**
     * Default Constructor
     *
     * This method's access modifier is set to private because
     * this class implements the Singleton pattern.
     *
     * @return void
     */
    private function __construct()
    {
        // Here you could have the code which calls your service/API's
        // '/auth' endpoint to get the authentication token, depending on
        // the type of security you are going to be using.

        // In this example, the repository is an in-memory storage and the
        // singleton pattern is used to make sure that there is only one
        // object during the lifespan of the application.
    }

    /**
     * __clone
     *
     * By leaving the clone method's workflow empty, we are preventing any code that
     * clones this object from executing and creating a second instance of this class.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * __wakeup
     *
     * It is possible to duplicate the object by serializing it and then instantiating a
     * new object of the same class. This method prevents objects of this class from being
     * serialized.
     *
     * @return void
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot Serialize");
    }

    /**
     * Set Products
     *
     * This method sets a collection of ServiceProducts to the
     * class' products property. This is used to set up the repository
     * and should not be used to update or add products.
     *
     * @param ExampleProduct[] $cp
     */
    public static function setProducts(array $cp)
    {
        self::$products = $cp;
    }

    /**
     * Get Products By Code
     *
     * This method returns the products from the repository.
     *
     * @param string[] $codes
     * @return ExampleProduct[]
     */
    public static function getProductsByCode(array $codes): array
    {
        $exampleProducts = [];
        foreach (self::$products as $p) {
            if (in_array($p->id, $codes)) {
                $exampleProducts[] = $p;
            }
        }
        return $exampleProducts;
    }

    /**
     * Unset Product By Code
     *
     * This method removes a product from the channel's state.
     *
     * @param string $code
     * @return void
     */
    public function unsetProductByCode($code)
    {
        /**
         * @var int $key The index of the current iteration of the loop.
         * @var ServiceProduct $p A product of the service type.
         */
        foreach (self::$products as $key => $p) {
            if ($p->id === $code) {
                unset(self::$products[$key]);
            }
        }
    }

    /**
     * Add Product
     *
     * This method adds a product to the '$products' property
     * of the channel's state. Only ServiceProducts are accepted.
     *
     * @param ServiceProduct $product
     * @return void
     */
    public function addProduct(ServiceProduct $product) {

        // Add product to state.
        self::$products[] = $product;

    }

}