<?php

namespace TalkBank\ApiBaaS;

use GuzzleHttp\Client as GuzzleClient;

/**
 * API for partners
 *   $client = new Client('http://localhost/api/v1/', '000a...', 'a00000....');
 *
 * @package TB\ApiPartners
 * @author  ploginoff
 */
class Client
{
    /**
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $partnerId;

    /**
     * Client constructor.
     * @param string $baseUri
     * @param string $partnerId
     * @param string $token
     */
    public function __construct(string $baseUri, string $partnerId, string $token)
    {
        $this->token = $token;
        $this->partnerId = $partnerId;
        $this->guzzle = new GuzzleClient(['base_uri' => $baseUri,]);
    }

    /**
     * Get account balance
     *
     * GET /api/v1/balance
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBalance() : array
    {
        return $this->exec('GET', 'balance');
    }

    /**
     * Get account history
     *
     * GET /api/v1/transactions
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTransactions() : array
    {
        return $this->exec('GET', 'transactions');
    }

    /**
     * Get card's transactions for the partner
     *
     * @param string $fromDate
     * @param string $toDate
     * @param int $page
     * @param int $limit
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCardsTransactions(
        string $fromDate,
        string $toDate,
        int $page = 1,
        int $limit = 1000) {
        return $this->exec(
            'GET',
            sprintf('cards-transactions'),
            [
                'fromDate'  => date('c', strtotime($fromDate)),
                'toDate'    => date('c', strtotime($toDate)),
                'page'      => $page,
                'limit'     => $limit,
            ]
        );
    }

    /**
     * Add new delivery
     *
     * @param string $clientId
     * @param array $params
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addDelivery(string $clientId, array $params)
    {
        return $this->exec('POST', sprintf('clients/%s/card-deliveries', $clientId), [], $params);
    }

    /**
     * Get info about delivery
     *
     * @param string $clientId
     * @param string $deliveryId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDelivery(string $clientId, string $deliveryId)
    {
        return $this->exec('GET', sprintf('clients/%s/card-deliveries/%s', $clientId, $deliveryId));
    }

    /**
     * Get card history
     *
     * @param string $clientId
     * @param string $barcode
     * @param string $fromDate
     * @param string $toDate
     * @param int $page
     * @param int $limit
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCardTransactions(
        string $clientId,
        string $barcode,
        string $fromDate,
        string $toDate,
        int $page = 1,
        int $limit = 1000) {
        return $this->exec(
            'GET',
            sprintf('clients/%s/cards/%s/transactions', $clientId, $barcode),
            [
                'fromDate'  => date('c', strtotime($fromDate)),
                'toDate'    => date('c', strtotime($toDate)),
                'page'      => $page,
                'limit'     => $limit,
            ]
        );
    }

    /**
     * Get callbacks
     *
     * GET /api/v1/event-subscriptions
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEventSubscriptions() : array
    {
        return $this->exec('GET', 'event-subscriptions');
    }

    /**
     * Refill card from account
     *
     * @param string $clientId
     * @param string $barcode
     * @param float $amount
     * @param string $orderId optional id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refill(string $clientId, string $barcode, float $amount, string $orderId = null)
    {
        if ($orderId) {
            $params = ['amount' => $amount, 'order_id' => $orderId,];
        } else {
            $params = ['amount' => $amount,];
        }
        return $this->exec('POST', sprintf('clients/%s/cards/%s/refill', $clientId, $barcode), [], $params);
    }

    /**
     * Withdraw money from the card
     *
     * @param string $clientId
     * @param string $barcode
     * @param float $amount
     * @param string $orderId optional id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function withdrawal(string $clientId, string $barcode, float $amount, string $orderId = null)
    {
        if ($orderId) {
            $params = ['amount' => $amount, 'order_id' => $orderId,];
        } else {
            $params = ['amount' => $amount,];
        }
        return $this->exec('POST', sprintf('clients/%s/cards/%s/withdrawal', $clientId, $barcode), [], $params);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $query
     * @param array $params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function exec(string $method, string $path, array $query = [], array $params = [])
    {
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $date = $date->format(DATE_RFC7231);

        // => /baas/api/v1/event-subscriptions
        $fullPath = parse_url($this->guzzle->getConfig('base_uri') . $path, PHP_URL_PATH);

        if ($params) {
            $body = json_encode($params, JSON_UNESCAPED_UNICODE);
        } else {
            $body = '';
        }

        $hashBody = hash('sha256', $body);

        $headers = array_change_key_case([
            'TB-Content-SHA256' => trim($hashBody),
            'Date' => trim($date),
        ], CASE_LOWER);

        ksort($headers);
        $headerString = [];

        foreach ($headers as $name => $value) {
            $headerString[] = $name . ':' . $value;
        }

        ksort($query);
        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        $string = $method . "\n"; // http verb
        $string .= $fullPath . "\n"; // uri
        $string .= $queryString . "\n"; // query
        $string .= implode("\n", $headerString) . "\n"; // headers
        $string .= $hashBody; // payload

        $signature = hash_hmac('sha256', $string, $this->token);

        $response = $this->guzzle->request($method, $path, [
            'query'     => $query,
            'body'      => $body,
            //  'debug'     => true,
            'headers'   => [
                'Content-Type'      => 'application/json',
                'TB-Content-SHA256' => trim($hashBody),
                'Date'              => trim($date),
                'Authorization'     => 'TB1-HMAC-SHA256 ' . $this->partnerId . ':' . $signature,
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
