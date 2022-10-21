<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Stock2Shop\Share;

class API
{
    private readonly string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return Product[]
     * @throws GuzzleException
     */
    public function getProducts(string $fromID, int $limit): array
    {
        // create guzzle client
        $client = new Client([
            'base_uri' => $this->url,
        ]);

        // execute request
        $response = $client->request('GET', '/products/page', [
            'query' => [
                'channel_product_code' => $fromID,
                'limit' => $limit
            ]
        ]);

        // get response body as json
        $body = json_decode($response->getBody()->getContents());
        return Product::createArray((array)$body);
    }

    /**
     * @param string[] $codes
     * @return Product[]
     * @throws GuzzleException
     */
    public function getProductsByCodes(array $codes): array
    {
        // create guzzle client
        $client = new Client([
            'base_uri' => $this->url,
        ]);

        // execute request
        $response = $client->request('GET', '/products', ['body' => json_encode($codes)]);


        // get response body as json
        $body = json_decode($response->getBody()->getContents());
        return Product::createArray((array)$body);
    }

    /**
     * @param Product[] $products
     * @return Product[]
     * @throws GuzzleException
     */
    public function postProducts(array $products): array
    {
        // create guzzle client
        $client = new Client([
            'base_uri' => $this->url,
        ]);

        // execute request
        $response = $client->request('POST', '/products', ['body' => json_encode($products)]);

        // get response body as json
        $body = json_decode($response->getBody()->getContents());
        return Product::createArray((array)$body);
    }

    public function deleteProducts($body)
    {
    }
}
