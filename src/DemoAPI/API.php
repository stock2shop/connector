<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Stock2Shop\Share;

class API
{
    private readonly string $url;
    private Client $client;

    public function __construct(string $url)
    {
        $this->url    = $url;
        $this->client = new Client([
            'base_uri' => $this->url,
        ]);
    }

    /**
     * @return Product[]
     * @throws GuzzleException
     */
    public function getProducts(string $fromID, int $limit): array
    {
        $response = $this->client->request('GET', '/products/page', [
            'query' => [
                'channel_product_code' => $fromID,
                'limit'                => $limit
            ]
        ]);
        return Product::createArray(
            json_decode($response->getBody()->getContents(), true)
        );
    }

    /**
     * @param string[] $codes
     * @return Product[]
     * @throws GuzzleException
     */
    public function getProductsByIDS(array $codes): array
    {
        $response = $this->client->request('GET', '/products', ['body' => json_encode($codes)]);
        return Product::createArray(
            json_decode($response->getBody()->getContents())
        );
    }

    /**
     * @param Product[] $products
     * @return Product[]
     * @throws GuzzleException
     */
    public function postProducts(array $products): array
    {
        $response = $this->client->request('POST', '/products', ['body' => json_encode($products)]);
        return Product::createArray(
            json_decode($response->getBody()->getContents(), true)
        );
    }

    /**
     * @param string[] $codes
     * @throws GuzzleException
     */
    public function deleteProducts(array $codes): int
    {
        $response = $this->client->request('DELETE', '/products', ['body' => json_encode($codes)]);
        return $response->getStatusCode();
    }
}
