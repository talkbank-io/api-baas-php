<?php

namespace TalkBank\ApiBaaS;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;

/**
 * API for partners
 *   $client = new Client('http://localhost/api/v1/', '000a...', 'a00000....');
 *
 * @package TB\ApiPartners
 * @author  ploginoff
 */
class Client
{
    public const DOCUMENT_UPLOAD_FIELD_NAME = 'upload';

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
     * @var bool
     */
    private $isDebug;

    /**
     * Client constructor.
     *
     * @param string $baseUri
     * @param string|null $partnerId
     * @param string|null $token
     * @param bool $isDebug
     */
    public function __construct(string $baseUri, ?string $partnerId = null, ?string $token = null, bool $isDebug = true)
    {
        $this->isDebug = $isDebug;
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
    public function accountBalance(): array
    {
        return $this->exec('GET', 'balance');
    }

    /**
     * @deprecated use Client::accountBalance() instead
     */
    public function getBalance(): array
    {
        return $this->accountBalance();
    }

    /**
     * Get operations
     *
     * GET /api/v1/operations
     */
    public function accountOperations(
        \DateTimeInterface $fromDate,
        \DateTimeInterface $toDate,
        int $page = 1,
        int $onPage = 50
    ): array {
        return $this->exec('GET', 'operations', [
            'from_date' => $fromDate->format('Y-m-d'),
            'to_date' => $toDate->format('Y-m-d'),
            'page' => $page,
            'on_page' => $onPage,
        ]);
    }

    /**
     * Get operation
     *
     * GET /api/v1/operations/{type}/{id}
     */
    public function accountOperation(string $type, string $id): array
    {
        return $this->exec('GET', sprintf('operations/%s/%s', $type, $id));
    }

    /**
     * Get client's cards
     *
     * GET /api/v1/clients/{client_id}/cards
     *
     * @param string $clientId
     * @return array
     */
    public function cardList(string $clientId): array
    {
        return $this->exec('GET', sprintf('clients/%s/cards', $clientId));
    }

    /**
     * Get card details
     *
     * GET /api/v1/clients/{client_id}/cards/{barcode}
     *
     * @param string $clientId
     * @param string $barcode
     * @return array
     */
    public function cardDetails(string $clientId, string $barcode): array
    {
        return $this->exec('GET', sprintf('clients/%s/cards/%s', $clientId, $barcode));
    }


    public function cardBarcode($cardNumber): array
    {
        return $this->exec('POST', 'cards/barcode', [] , ['number' => $cardNumber]);
    }


    /**
     * Get direct transaction's status, this is alias for `payment_status`
     *
     * GET /api/v1/clients/{client_id}/cards/{barcode}/{order_id}
     *
     * @param string $clientId
     * @param string $barcode
     * @param string $orderId
     * @return array
     */
    public function cardOrderStatus(string $clientId, string $barcode, string $orderId): array
    {
        return $this->exec('GET', sprintf('clients/%s/cards/%s/%s', $clientId, $barcode, $orderId));
    }

    /**
     * Get card balance
     *
     * GET /api/v1/clients/{client_id}/cards/{barcode}/balance
     *
     * @param string $clientId
     * @param string $barcode
     * @return array
     */
    public function cardBalance(string $clientId, string $barcode): array
    {
        return $this->exec('GET', sprintf('clients/%s/cards/%s/balance', $clientId, $barcode));
    }

    /**
     * Get card status
     *
     * GET /api/v1/clients/{client_id}/cards/{barcode}/lock
     *
     * @param string $clientId
     * @param string $barcode
     * @return array
     */
    public function cardLockStatus(string $clientId, string $barcode): array
    {
        return $this->exec('GET', sprintf('clients/%s/cards/%s/lock', $clientId, $barcode));
    }

    /**
     * Block the card
     *
     * POST /api/v1/clients/{client_id}/cards/{barcode}/lock
     *
     * @param string $clientId
     * @param string $barcode
     * @param string|null $reason
     * @return array
     */
    public function cardLock(string $clientId, string $barcode, ?string $reason = null): array
    {
        $params = $this->filterParams(['reason' => $reason]);

        return $this->exec('POST', sprintf('clients/%s/cards/%s/lock', $clientId, $barcode), [], $params);
    }

    /**
     * Unblock the card
     *
     * DELETE /api/v1/clients/{client_id}/cards/{barcode}/lock
     *
     * @param string $clientId
     * @param string $barcode
     * @return array
     */
    public function cardUnlock(string $clientId, string $barcode): array
    {
        return $this->exec('DELETE', sprintf('clients/%s/cards/%s/lock', $clientId, $barcode));
    }

    /**
     * Create virtual card
     *
     * POST /api/v1/clients/{client_id}/virtual-cards
     *
     * @param string $clientId
     * @return array
     */
    public function cardActivateVirtual(string $clientId): array
    {
        return $this->exec('POST', sprintf('clients/%s/virtual-cards', $clientId));
    }

    /**
     * Activate card
     *
     * POST /api/v1/clients/{client_id}/cards/{barcode}/activate
     *
     * @param string $clientId
     * @param string $barcode
     * @param string|null $type
     * @return array
     */
    public function cardActivate(string $clientId, string $barcode, ?string $type = null): array
    {
        $params = $this->filterParams(['type' => $type]);

        return $this->exec('POST', sprintf('clients/%s/cards/%s/activate', $clientId, $barcode), [], $params);
    }

    /**
     * Get card activation status
     *
     * GET /api/v1/clients/{client_id}/cards/{barcode}/activation
     *
     * @param string $clientId
     * @param string $barcode
     * @return array
     */
    public function cardActivation(string $clientId, string $barcode): array
    {
        return $this->exec('GET', sprintf('clients/%s/cards/%s/activation', $clientId, $barcode));
    }

    /**
     * Sending a CVV on the client's phone
     *
     * GET /api/v1/clients/{client_id}/cards/{barcode}/security-code
     *
     * @param string $clientId
     * @param string $barcode
     * @return array
     */
    public function cardCvv(string $clientId, string $barcode): array
    {
        return $this->exec('GET', sprintf('clients/%s/cards/%s/security-code', $clientId, $barcode));
    }

    /**
     * Get cardholder data
     *
     * GET /api/v1/clients/{client_id}/cards/{barcode}/cardholder/data
     *
     * @param string $clientId
     * @param string $barcode
     * @return array
     */
    public function cardCardholderData(string $clientId, string $barcode): array
    {
        return $this->exec('GET', sprintf('clients/%s/cards/%s/cardholder/data', $clientId, $barcode));
    }

    /**
     * Get card limits
     *
     * GET /api/v1/clients/{client_id}/cards/{barcode}/limits
     *
     * @param string $clientId
     * @param string $barcode
     * @return array
     */
    public function cardLimits(string $clientId, string $barcode): array
    {
        return $this->exec('GET', sprintf('clients/%s/cards/%s/limits', $clientId, $barcode));
    }

    /**
     * Hold money from registered or unregistered card
     *
     * POST /api/v1/hold
     *
     * @param int|null $amount
     * @param string|null $orderSlug
     * @param array|null $cardInfo
     * @param string|null $cardRefId
     * @param string|null $redirectUrl
     * @return array
     */
    public function hold(
        ?int $amount = null,
        ?string $orderSlug = null,
        ?array $cardInfo = null,
        ?string $cardRefId = null,
        ?string $redirectUrl = null
    ): array {
        $params = $this->filterParams([
            'amount' => $amount,
            'order_slug' => $orderSlug,
            'card_info' => $cardInfo,
            'card_ref_id' => $cardRefId,
            'redirect_url' => $redirectUrl,
        ]);

        return $this->exec('POST', 'hold', [], $params);
    }

    /**
     * Hold money from registered or unregistered card with payment form
     *
     * POST /api/v1/hold/{client_id}/with/form
     *
     * @param string $clientId
     * @param string|null $redirectUrl
     * @param int|null $amount
     * @param string|null $orderSlug
     * @param string|null $cardToken
     * @return array
     */
    public function holdWithForm(
        string $clientId,
        string $redirectUrl,
        int $amount,
        ?string $orderSlug = null,
        ?string $cardToken = null
    ): array {
        $params = $this->filterParams([
            'redirect_url' => $redirectUrl,
            'amount' => $amount,
            'order_slug' => $orderSlug,
            'card_token' => $cardToken,
        ]);

        return $this->exec('POST', sprintf('hold/%s/with/form', $clientId), [], $params);
    }

    /**
     * Confirm full or partial hold
     *
     * POST /api/v1/hold/confirm/{order_slug}
     *
     * @param string $orderSlug
     * @param int|null $amount
     * @return array
     */
    public function holdConfirm(string $orderSlug, ?int $amount = null): array
    {
        $params = $this->filterParams(['amount' => $amount]);

        return $this->exec('POST', sprintf('hold/confirm/%s', $orderSlug), [], $params);
    }

    /**
     * Reverse hold
     *
     * POST /api/v1/hold/reverse/{order_slug}
     *
     * @param string $orderSlug
     * @param int|null $amount
     * @return array
     */
    public function holdReverse(string $orderSlug, ?int $amount = null): array
    {
        $params = $this->filterParams(['amount' => $amount]);

        return $this->exec('POST', sprintf('hold/reverse/%s', $orderSlug), [], $params);
    }

    /**
     * Get transactions for all partner's cards
     *
     * @param string $fromDate
     * @param string $toDate
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function accountCardsTransactions(
        string $fromDate,
        string $toDate,
        int $page = 1,
        int $limit = 1000
    ): array {
        $params = $this->filterParams([
            'fromDate' => date('c', strtotime($fromDate)),
            'toDate' => date('c', strtotime($toDate)),
            'page' => $page,
            'limit' => $limit,
        ]);

        return $this->exec('GET', 'cards-transactions', $params);
    }

    /**
     * @deprecated use Client::accountCardsTransactions() instead
     */
    public function getCardsTransactions(
        string $fromDate,
        string $toDate,
        int $page = 1,
        int $limit = 1000
    ) {
        return $this->accountCardsTransactions($fromDate, $toDate, $page, $limit);
    }

    /**
     * Add new delivery
     *
     * POST /api/v1/clients/{client_id}/card-deliveries
     *
     * @param string $clientId
     * @param array $params
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cardDeliveryStore(string $clientId, array $params): array
    {
        return $this->exec('POST', sprintf('clients/%s/card-deliveries', $clientId), [], $params);
    }

    /**
     * @deprecated Synonym of the method Client::cardDeliveryStore(), use this one instead
     */
    public function addDelivery(string $clientId, array $params): array
    {
        return $this->cardDeliveryStore($clientId, $params);
    }

    /**
     * Get info about delivery
     *
     * GET ​/api​/v1​/clients​/{client_id}​/card-deliveries​/{delivery_id}
     *
     * @param string $clientId
     * @param string $deliveryId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cardDeliveryShow(string $clientId, string $deliveryId): array
    {
        return $this->exec('GET', sprintf('clients/%s/card-deliveries/%s', $clientId, $deliveryId));
    }

    /**
     * @deprecated Synonym of the method Client::cardDeliveryShow(), use this one instead
     */
    public function getDelivery(string $clientId, string $deliveryId)
    {
        return $this->exec('GET', sprintf('clients/%s/card-deliveries/%s', $clientId, $deliveryId));
    }

    /**
     * Get card history
     *
     * GET ​/api​/v1​/clients​/{client_id}​/cards​/{barcode}​/transactions
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
    public function cardTransactions(
        string $clientId,
        string $barcode,
        string $fromDate,
        string $toDate,
        int $page = 1,
        int $limit = 1000
    ): array {
        return $this->exec(
            'GET',
            sprintf('clients/%s/cards/%s/transactions', $clientId, $barcode),
            [
                'fromDate' => date('c', strtotime($fromDate)),
                'toDate' => date('c', strtotime($toDate)),
                'page' => $page,
                'limit' => $limit,
            ]
        );
    }

    /**
     * @deprecated Synonym of the method Client::cardTransactions(), use this one instead
     */
    public function getCardTransactions(
        string $clientId,
        string $barcode,
        string $fromDate,
        string $toDate,
        int $page = 1,
        int $limit = 1000
    ): array {
        return $this->cardTransactions($clientId, $barcode, $fromDate, $toDate, $page, $limit);
    }

    /**
     * Get callbacks
     *
     * GET /api/v1/event-subscriptions
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function eventSubscriptionList(): array
    {
        return $this->exec('GET', 'event-subscriptions');
    }

    /**
     * @deprecated Synonym of the method Client::eventSubscriptionList(), use this one instead
     */
    public function getEventSubscriptions(): array
    {
        return $this->eventSubscriptionList();
    }

    /**
     * Add event subscription
     *
     * POST /api/v1/event-subscriptions
     *
     * @param string $url
     * @param array $events
     * @return array
     */
    public function eventSubscriptionStore(string $url, array $events = []): array
    {
        return $this->exec('POST', 'event-subscriptions', [], [
            'url' => $url,
            'events' => $events,
        ]);
    }

    /**
     * Delete subscription
     *
     * DELETE /api/v1/event-subscriptions/{subscription_id}
     *
     * @param string $subscriptionId
     * @return array
     */
    public function eventSubscriptionRemove(string $subscriptionId): array
    {
        return $this->exec('DELETE', sprintf('event-subscriptions/%s', $subscriptionId));
    }

    /**
     * GET /api/v1/charge/unregistered/card/{order_id}
     *
     * @param string $orderId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @deprecated
     */
    public function getStatusChargeUnregisteredCard(string $orderId)
    {
        return $this->exec('GET', sprintf('charge/unregistered/card/%s', $orderId));
    }

    /**
     * Refill card from account
     *
     * POST /api/v1/clients/{client_id}/cards/{barcode}/refill
     */
    public function cardRefill(
        string $clientId,
        string $barcode,
        int $amount,
        ?string $orderId = null,
        bool $percentsOnUser = false,
        ?string $receiptId = null,
        ?string $beneficiaryId = null,
        ?array $receiptIds = null
    ): array {
        $params = $this->filterParams([
            'amount' => $amount,
            'order_slug' => $orderId,
            'percents_on_user' => $percentsOnUser,
            'receipt_id' => $receiptId,
            'beneficiary_id' => $beneficiaryId,
            'receipt_ids' => $receiptIds,
        ]);

        return $this->exec('POST', sprintf('clients/%s/cards/%s/refill', $clientId, $barcode), [], $params);
    }

    /**
     * @deprecated Synonym of the method Client::cardRefill(), use this one instead
     */
    public function refill(string $clientId, string $barcode, float $amount, ?string $orderId = null): array
    {
        return $this->cardRefill($clientId, $barcode, $amount, $orderId);
    }

    /**
     * Withdraw money from the card
     *
     * POST /api/v1/clients/{client_id}/cards/{barcode}/withdrawal
     *
     * @param string $clientId
     * @param string $barcode
     * @param float $amount
     * @param string $orderId optional id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cardWithdrawal(string $clientId, string $barcode, float $amount, ?string $orderId = null): array
    {
        $params = $this->filterParams([
            'amount' => $amount,
            'order_id' => $orderId,
        ]);

        return $this->exec('POST', sprintf('clients/%s/cards/%s/withdrawal', $clientId, $barcode), [], $params);
    }

    /**
     * @deprecated Synonym of the method Client::cardWithdrawal(), use this one instead
     */
    public function withdrawal(string $clientId, string $barcode, float $amount, ?string $orderId = null)
    {
        return $this->cardWithdrawal($clientId, $barcode, $amount, $orderId);
    }

    /**
     * Charge/withdraw money from card to account
     *
     * POST /api/v1/charge/{client_id}/unregistered/card
     *
     * @param string $clientId
     * @param int $amount
     * @param array $cardInfo
     * @param string|null $redirectUrl
     * @param string|null $orderSlug
     * @return array
     */
    public function paymentFromUnregisteredCard(
        string $clientId,
        int $amount,
        array $cardInfo,
        ?string $redirectUrl = null,
        ?string $orderSlug = null
    ): array {
        $params = $this->filterParams([
            'amount' => $amount,
            'card_info' => $cardInfo,
            'redirect_url' => $redirectUrl,
            'order_slug' => $orderSlug,
        ]);

        return $this->exec('POST', sprintf('charge/%s/unregistered/card', $clientId), [], $params);
    }

    /**
     * Create token for clientCharge
     *
     * POST /api/v1/charge/{client_id}/token
     *
     * @param string $clientId
     * @param string $redirectUrl
     * @param int $amount
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function paymentFromUnregisteredCardToken(string $clientId, string $redirectUrl, int $amount): array
    {
        return $this->exec('POST', sprintf('charge/%s/token', $clientId), [], [
            'redirect_url' => $redirectUrl,
            'amount' => $amount,
        ]);
    }

    /**
     * @deprecated Synonym of the method Client::paymentFromUnregisteredCardToken(), use this one instead
     */
    public function chargeToken(string $clientId, string $redirectUrl, int $amount): array
    {
        return $this->paymentFromUnregisteredCardToken($clientId, $redirectUrl, $amount);
    }

    /**
     * POST /api/v1/refill/{client_id}/token
     *
     * @param string $clientId
     * @param int $amount
     * @param string|null $orderSlug
     * @return array
     */
    public function paymentToUnregisteredCardToken(string $clientId, int $amount, ?string $orderSlug = null): array
    {
        $params = $this->filterParams([
            'amount' => $amount,
            'order_slug' => $orderSlug,
        ]);

        return $this->exec('POST', sprintf('refill/%s/token', $clientId), [], $params);
    }

    /**
     * POST /api/v1/charge/{client_id}/unregistered/card/with/form
     *
     * @param string $clientId
     * @param int $amount
     * @param string|null $orderSlug
     * @param string|null $redirectUrl
     * @return array
     */
    public function paymentFromUnregisteredCardWithForm(
        string $clientId,
        int $amount,
        ?string $orderSlug = null,
        ?string $redirectUrl = null
    ): array {
        $params = $this->filterParams([
            'amount' => $amount,
            'order_slug' => $orderSlug,
            'redirect_url' => $redirectUrl,
        ]);

        return $this->exec('POST', sprintf('charge/%s/unregistered/card/with/form', $clientId), [], $params);
    }

    /**
     * Charge card without 3ds
     *
     * POST /api/v1/payment/from/{client_id}/registered/card
     *
     * @param string $clientId
     * @param int $amount
     * @param string $cardToken
     * @param string|null $orderSlug
     * @return array
     */
    public function paymentFromRegisteredCard(
        string $clientId,
        int $amount,
        string $cardToken,
        ?string $orderSlug = null
    ): array {
        $params = $this->filterParams([
            'amount' => $amount,
            'card_token' => $cardToken,
            'order_slug' => $orderSlug,
        ]);

        return $this->exec('POST', sprintf('payment/from/%s/registered/card', $clientId), [], $params);
    }

    /**
     * Refill card from account
     *
     * POST /api/v1/authorize/card/{client_id}
     *
     * @param string $clientId
     * @param array $cardInfo
     * @param string|null $redirectUrl
     * @param int|null $amount
     * @return array
     */
    public function paymentAuthorization(
        string $clientId,
        array $cardInfo,
        ?string $redirectUrl = null,
        ?int $amount = null
    ): array {
        $params = $this->filterParams([
            'card_info' => $cardInfo,
            'redirect_url' => $redirectUrl,
            'amount' => $amount,
        ]);

        return $this->exec('POST', sprintf('authorize/card/%s', $clientId), [], $params);
    }

    /**
     * Get tokens for authorization on a client side
     *
     * POST /api/v1/authorize/card/{client_id}/token
     *
     * @param string $clientId
     * @param string|null $redirectUrl
     * @param int|null $amount
     * @return array
     */
    public function paymentAuthorizationToken(
        string $clientId,
        ?string $redirectUrl = null,
        ?int $amount = null
    ): array {
        $params = $this->filterParams([
            'redirect_url' => $redirectUrl,
            'amount' => $amount,
        ]);

        return $this->exec('POST', sprintf('authorize/card/%s/token', $clientId), [], $params);
    }

    /**
     * POST /api/v1/authorize/card/{client_id}/with/form
     *
     * @param string $clientId
     * @param string|null $redirectUrl
     * @param string|null $orderSlug
     * @param int|null $amount
     * @return array
     */
    public function paymentAuthorizationWithForm(
        string $clientId,
        ?string $redirectUrl = null,
        ?string $orderSlug = null,
        ?int $amount = null
    ): array {
        $params = $this->filterParams([
            'redirect_url' => $redirectUrl,
            'order_slug' => $orderSlug,
            'amount' => $amount,
        ]);

        return $this->exec('POST', sprintf('authorize/card/%s/with/form', $clientId), [], $params);
    }

    /**
     * POST /api/v1/payment/to/{client_id}/registered/card
     */
    public function paymentToRegisteredCard(
        string $clientId,
        string $cardToken,
        int $amount,
        ?string $orderSlug,
        bool $percentsOnUser = false,
        ?string $receiptId = null,
        ?string $beneficiaryId = null,
        ?array $receiptIds = null
    ): array {
        $params = $this->filterParams([
            'card_token' => $cardToken,
            'amount' => $amount,
            'order_slug' => $orderSlug,
            'percents_on_user' => $percentsOnUser,
            'receipt_id' => $receiptId,
            'beneficiary_id' => $beneficiaryId,
            'receipt_ids' => $receiptIds,
        ]);

        return $this->exec('POST', sprintf('payment/to/%s/registered/card', $clientId), [], $params);
    }

    /**
     * Set PIN code for card (only for RFI now)
     *
     * POST /api/v1/clients/{client_id}/cards/{barcode}/set/pin
     *
     * @param string $clientId
     * @param string $barcode
     * @param string $pinCode
     * @return array
     */
    public function setCardPin(string $clientId, string $barcode, string $pinCode): array
    {
        return $this->exec('POST', sprintf('clients/%s/cards/%s/set/pin', $clientId, $barcode), [], [
            'pin' => $pinCode
        ]);
    }

    /**
     * Payment to account
     *
     * POST /api/v1/account/transfer
     */
    public function paymentToAccount(
        int $amount,
        string $account,
        string $bik,
        string $name,
        ?string $inn = null,
        ?string $description = null,
        ?string $orderSlug = null,
        ?string $receiptId = null,
        ?string $beneficiaryId = null,
        ?array $receiptIds = null,
        ?string $incomeCode = null,
        ?string $currencyControlFileId = null
    ): array {
        $params = $this->filterParams([
            'amount' => $amount,
            'account' => $account,
            'bik' => $bik,
            'name' => $name,
            'inn' => $inn,
            'description' => $description,
            'order_slug' => $orderSlug,
            'receipt_id' => $receiptId,
            'beneficiary_id' => $beneficiaryId,
            'receipt_ids' => $receiptIds,
            'income_code' => $incomeCode,
            'currency_control_file_id' => $currencyControlFileId
        ]);

        return $this->exec('POST', 'account/transfer', [], $params);
    }

    /**
     * Payment to account with tax data
     *
     * POST /api/v1/account/transfer/tax
     */
    public function paymentToAccountWithTaxData(
        int $amount,
        string $account,
        string $bik,
        string $name,
        string $inn,
        string $kpp,
        string $kbk,
        string $okato,
        string $paymentBase,
        string $taxPeriod,
        string $taxDocNum,
        string $taxDocDate,
        string $uin,
        string $payerInn,
        ?string $payerName = null,
        ?string $description = null,
        ?string $orderSlug = null,
        ?string $formerStatus = null
    ): array {
        $params = $this->filterParams([
            'amount' => $amount,
            'account' => $account,
            'bik' => $bik,
            'name' => $name,
            'inn' => $inn,
            'kpp' => $kpp,
            'kbk' => $kbk,
            'okato' => $okato,
            'payment_base' => $paymentBase,
            'tax_period' => $taxPeriod,
            'tax_doc_num' => $taxDocNum,
            'tax_doc_date' => $taxDocDate,
            'uin' => $uin,
            'payer_inn' => $payerInn,
            'payer_name' => $payerName,
            'description' => $description,
            'order_slug' => $orderSlug,
            'former_status' => $formerStatus
        ]);

        return $this->exec('POST', 'account/transfer/tax', [], $params);
    }

    /**
     * GET /api/v1/account/transfer/{order_slug}
     */
    public function paymentToAccountStatus(string $orderSlug): array
    {
        return $this->exec('GET', 'account/transfer/' . $orderSlug);
    }

    /**
     * Refill card by cardNumber
     *
     * POST /api/v1/refill/unregistered/card
     */
    public function paymentToUnregisteredCard(
        string $cardNumber,
        ?int $amount,
        ?string $orderSlug,
        bool $percentsOnUser = false,
        ?string $receiptId = null,
        ?string $beneficiaryId = null,
        ?array $receiptIds = null
    ): array {
        $params = [
            'card_number' => $cardNumber,
            'amount' => $amount,
            'order_slug' => $orderSlug,
            'percents_on_user' => $percentsOnUser,
            'receipt_id' => $receiptId,
            'beneficiary_id' => $beneficiaryId,
            'receipt_ids' => $receiptIds,
        ];

        return $this->exec('POST', 'refill/unregistered/card', [], $params);
    }

    /**
     * POST /api/v1/refill/{client_id}/unregistered/card/with/form
     *
     * @param string $clientId
     * @param int $amount
     * @param string|null $orderSlug
     * @param string|null $redirectUrl
     * @param bool $percentsOnUser
     * @param string|null $receiptId
     * @param string|null $beneficiaryId
     * @param array|null $receiptIds
     * @return array
     */
    public function paymentToUnregisteredCardWithForm(
        string $clientId,
        int $amount,
        ?string $orderSlug = null,
        ?string $redirectUrl = null,
        bool $percentsOnUser = false,
        ?string $receiptId = null,
        ?string $beneficiaryId = null,
        ?array $receiptIds = null
    ): array {
        $params = $this->filterParams([
            'amount' => $amount,
            'order_slug' => $orderSlug,
            'redirect_url' => $redirectUrl,
            'percents_on_user' => $percentsOnUser,
            'receipt_id' => $receiptId,
            'beneficiary_id' => $beneficiaryId,
            'receipt_ids' => $receiptIds,
        ]);

        return $this->exec('POST', sprintf('refill/%s/unregistered/card/with/form', $clientId), [], $params);
    }

    /**
     * Get direct payment status
     *
     * GET /api/v1/payment/{order_slug}
     *
     * @param string $orderSlug
     * @return array
     */
    public function paymentStatus(string $orderSlug): array
    {
        return $this->exec('GET', sprintf('payment/%s', $orderSlug));
    }

    /**
     * Get direct payment receipt
     *
     * GET /api/v1/payment/{order_slug}/receipt
     */
    public function paymentReceipt(string $orderSlug): array
    {
        return $this->exec('GET', sprintf('payment/%s/receipt', $orderSlug));
    }

    /**
     * Get identification pdf for Client/Card (a few banks only)
     *
     * GET /api/v1/clients/{client_id}/pdf
     *
     * @param string $clientId
     * @return mixed
     */
    public function clientPdf(string $clientId)
    {
        return $this->exec('GET', sprintf('clients/%s/pdf', $clientId));
    }

    /**
     * Get agreement docx (pdf) for Client/Card (a few banks only)
     *
     * GET /api/v1/clients/{client_id}/agreement
     *
     * @param string $clientId
     * @return mixed
     */
    public function clientAgreement(string $clientId)
    {
        return $this->exec('GET', sprintf('clients/%s/agreement', $clientId));
    }

    /**
     * Charge method for Client (w/o signature!)
     *
     * POST /client/v1/charge
     *
     * @param string $token
     * @param int $amount
     * @param array $cardInfo
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @todo to add unit test
     */
    public function unsignedPaymentFromUnregisteredCard(string $token, int $amount, array $cardInfo)
    {
        $params = [
            'token' => $token,
            'amount' => $amount,
            'card_info' => $cardInfo,
        ];

        $response = $this->guzzle->request('POST', '/client/v1/charge', [
            'json' => $params,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @deprecated Synonym of the method Client::unsignedPaymentFromUnregisteredCard(), use this one instead
     */
    public function clientCharge(string $token, int $amount, array $cardInfo)
    {
        return $this->unsignedPaymentFromUnregisteredCard($token, $amount, $cardInfo);
    }

    /**
     * Create the client
     *
     * POST /api/v1/clients
     *
     * @param string $clientId
     * @param array $person
     * @return array
     */
    public function clientStore(string $clientId, array $person): array
    {
        return $this->exec('POST', 'clients', [], [
            'client_id' => $clientId,
            'person' => $person
        ]);
    }

    /**
     * Update the client
     *
     * PUT /api/v1/clients/{client_id}
     *
     * @param string $clientId
     * @param array $person
     * @return array
     */
    public function clientEdit(string $clientId, array $person): array
    {
        return $this->exec('PUT', sprintf('clients/%s', $clientId), [], [
            'client_id' => $clientId,
            'person' => $person
        ]);
    }

    /**
     * Get client's status
     *
     * GET /api/v1/clients/{client_id}
     *
     * @param string $clientId
     * @return array
     */
    public function clientShow(string $clientId): array
    {
        return $this->exec('GET', sprintf('clients/%s', $clientId));
    }

    /**
     * GET /api/v1/selfemployments/{client_id}
     *
     * @param string $clientId
     * @return array
     */
    public function selfemploymentsRegistrationStatus(string $clientId): array
    {
        return $this->exec('GET', sprintf('selfemployments/%s', $clientId));
    }

    /**
     * POST /api/v1/selfemployments/{client_id}/bind
     *
     * @param string $clientId
     * @param string $requestId
     * @return array
     */
    public function bindSelfemployments(string $clientId, string $requestId): array
    {
        return $this->exec('POST', sprintf('selfemployments/%s/bind', $clientId), [], [
            'RequestId' => $requestId,
        ]);
    }

    /**
     * POST /api/v1/selfemployments/{client_id}/receipt-async
     *
     * @param string        $clientId
     * @param string        $operationTime
     * @param array         $services
     * @param float         $totalAmount
     * @param string        $incomeType
     * @param string | null $requestTime
     * @param string | null $operationUniqueId
     * @param string | null $customerInn
     * @param string | null $customerOrganization
     * @param string | null $supplierInn
     * @param array | null  $geoInfo
     *
     * @return array
     */
    public function registerReceiptAsync(
        string $clientId,
        string $operationTime,
        array $services,
        float $totalAmount,
        string $incomeType,
        ?string $requestTime = null,
        ?string $operationUniqueId = null,
        ?string $customerInn = null,
        ?string $customerOrganization = null,
        ?string $supplierInn = null,
        ?array $geoInfo = null
    ): array {
        return $this->exec('POST', sprintf('selfemployments/%s/receipt-async', $clientId), [], $this->filterParams([
            'RequestTime' => $requestTime,
            'OperationTime' => $operationTime,
            'Services' => $services,
            'TotalAmount' => $totalAmount,
            'IncomeType' => $incomeType,
            'OperationUniqueId' => $operationUniqueId,
            'CustomerInn' => $customerInn,
            'CustomerOrganization' => $customerOrganization,
            'SupplierInn' => $supplierInn,
            'GeoInfo' => $geoInfo,
        ]));
    }

    /**
     * GET /api/v1/selfemployments/{client_id}/income_reference
     *
     * @param string $clientId
     * @param string $requestYear (YYYY)
     * @return array
     */
    public function selfemploymentsIncomeReference(string $clientId, string $requestYear): array
    {
        return $this->exec('GET', sprintf('selfemployments/%s/income_reference', $clientId), [
            'requestYear' => $requestYear,
        ]);
    }

    /**
     * GET /api/v1/selfemployments/{client_id}/registration_reference
     *
     * @param string $clientId
     * @param string $requestYear (YYYY)
     * @return array
     */
    public function selfemploymentsRegistrationReference(string $clientId, string $requestYear): array
    {
        return $this->exec('GET', sprintf('selfemployments/%s/registration_reference', $clientId), [
            'requestYear' => $requestYear,
        ]);
    }

    /**
     * GET /api/v1/selfemployments/{client_id}/account_status
     *
     * @param string $clientId
     * @return array
     */
    public function selfemploymentsAccountStatus(string $clientId): array
    {
        return $this->exec('GET', sprintf('selfemployments/%s/account_status', $clientId));
    }

    /**
     * GET /api/v1/selfemployments/{client_id}/income
     *
     * @param string $clientId
     * @param string $taxPeriod (YYYYMM)
     * @return array
     */
    public function selfemploymentsIncome(string $clientId, string $taxPeriod): array
    {
        return $this->exec('GET', sprintf('selfemployments/%s/income', $clientId), [
            'taxPeriod' => $taxPeriod,
        ]);
    }

    /**
     * POST /api/v1/selfemployments/{client_id}/check_bind
     *
     * @param string $clientId
     * @param string $requestId
     * @return array
     */
    public function checkBindSelfemployments(string $clientId, string $requestId): array
    {
        return $this->exec('POST', sprintf('selfemployments/%s/check_bind', $clientId), [], [
            'RequestId' => $requestId,
        ]);
    }

    /**
     * Refill a card on the client-side using a temp token,
     * see payment_to_unregistered_card_token & payment_to_unregistered_card_with_form
     *
     * POST /client/v1/refill
     *
     * @param string $token
     * @param string $cardNumber
     * @return array
     */
    public function unsignedPaymentToUnregisteredCard(string $token, string $cardNumber): array
    {
        return $this->exec('POST', 'client/v1/refill', [], [
            'token' => $token,
            'card_number' => $cardNumber,
        ]);
    }

    /**
     * POST /client/v1/authorize
     *
     * @param string $token
     * @param array $cardInfo
     * @return array
     */
    public function unsignedPaymentAuthorization(string $token, array $cardInfo): array
    {
        return $this->exec('POST', 'client/v1/authorize', [], [
            'token' => $token,
            'card_info' => $cardInfo,
        ]);
    }

    /**
     * Hold card on the client-side
     *
     * POST /client/v1/hold
     *
     * @param string $token
     * @param array $cardInfo
     * @return array
     */
    public function unsignedHold(string $token, array $cardInfo): array
    {
        return $this->exec('POST', 'client/v1/hold', [], [
            'token' => $token,
            'card_info' => $cardInfo,
        ]);
    }

    /**
     * Get status by token
     *
     * GET /client/v1/status/{hash}
     *
     * @param string $hash
     * @return array
     */
    public function unsignedPaymentStatusByHash(string $hash): array
    {
        return $this->exec('GET', sprintf('client/v1/status/%s', $hash));
    }

    /**
     * Change limit for card
     *
     * POST /api/v1/clients/{client_id}/cards/{barcode}/limits
     *
     * @param string $clientId
     * @param string $barcode
     * @param string $name
     * @param int $amount
     * @return array
     */
    public function changeCardLimit(string $clientId, string $barcode, string $name, int $amount): array
    {
        return $this->exec('POST', sprintf('clients/%s/cards/%s/limits', $clientId, $barcode), [], [
            'name' => $name,
            'amount' => $amount,
        ]);
    }

    /**
     * Refund payment
     *
     * POST /api/v1/refund
     */
    public function refund(string $orderSlug, ?int $amount = null): array
    {
        return $this->exec('POST', 'refund', [], [
            'order_slug' => $orderSlug,
            'amount' => $amount,
        ]);
    }

    /**
     * Reverse payment
     *
     * POST /api/v1/reverse
     */
    public function reverse(string $orderSlug): array
    {
        return $this->exec('POST', 'reverse', [], ['order_slug' => $orderSlug]);
    }

    /**
     * Create itelier order in marketplace
     *
     * POST /api/v1/marketplace/itelier/order
     */
    public function itelierCreateOrder(
        string $atelierId,
        string $clientId,
        string $clientName,
        string $transactionId,
        array $transactionDetails
    ): array {
        return $this->exec('POST', 'marketplace/itelier/order', [], [
            'atelier_id' => $atelierId,
            'client_id' => $clientId,
            'client_name' => $clientName,
            'transaction_id' => $transactionId,
            'transaction_details' => $transactionDetails
        ]);
    }

    /**
     * Create itelier atelier in marketplace
     *
     * POST /api/v1/marketplace/itelier/atelier
     */
    public function itelierCreateAtelier(
        string $atelierId,
        string $atelierName,
        array $atelierDetails
    ): array {
        return $this->exec('POST', 'marketplace/itelier/atelier', [], [
            'atelier_id' => $atelierId,
            'atelier_name' => $atelierName,
            'atelier_details' => $atelierDetails
        ]);
    }

    /**
     * GET /api/v1/beneficiaries
     */
    public function beneficiaryList(): array
    {
        return $this->exec('GET', 'beneficiaries');
    }

    /**
     * POST /api/v1/beneficiaries
     */
    public function beneficiaryAdd(string $name, string $inn, bool $isBlocked = false): array
    {
        return $this->exec('POST', 'beneficiaries', [], [
            'name' => $name,
            'inn' => $inn,
            'is_blocked' => $isBlocked,
        ]);
    }

    /**
     * PUT /api/v1/beneficiaries/{beneficiary_id}
     */
    public function beneficiaryEdit(string $beneficiaryId, array $params): array
    {
        return $this->exec('PUT', \sprintf('beneficiaries/%s', $beneficiaryId), [], $params);
    }

    /**
     * PUT /api/v1/beneficiaries/{beneficiary_id}
     */
    public function beneficiaryBlock(string $beneficiaryId): array
    {
        return $this->exec('PUT', \sprintf('beneficiaries/%s', $beneficiaryId), [], [
            'is_blocked' => true,
        ]);
    }

    /**
     * PUT /api/v1/beneficiaries/{beneficiary_id}
     */
    public function beneficiaryUnblock(string $beneficiaryId): array
    {
        return $this->exec('PUT', \sprintf('beneficiaries/%s', $beneficiaryId), [], [
            'is_blocked' => false,
        ]);
    }

    /**
     * GET /api/v1/beneficiaries/{beneficiary_id}
     */
    public function beneficiaryShow(string $beneficiaryId): array
    {
        return $this->exec('GET', \sprintf('beneficiaries/%s', $beneficiaryId));
    }

    /**
     * GET /api/v1/beneficiaries/{beneficiary_id}/commissions
     */
    public function beneficiaryCommissionList(string $beneficiaryId): array
    {
        return $this->exec('GET', \sprintf('beneficiaries/%s/commissions', $beneficiaryId));
    }

    /**
     * POST /api/v1/beneficiaries/{beneficiary_id}/commissions
     */
    public function beneficiaryCommissionAdd(
        string $beneficiaryId,
        \DateTimeInterface $willStartAt,
        ?float $percentage = null,
        ?int $fixedPart = null,
        ?int $minimum = null
    ): array {
        return $this->exec('POST', \sprintf('beneficiaries/%s/commissions', $beneficiaryId), [], [
            'will_start_at' => $willStartAt->format('Y-m-d H:i:s e'),
            'percentage' => $percentage,
            'fixed_part' => $fixedPart,
            'minimum' => $minimum,
        ]);
    }

    /**
     * PUT /api/v1/beneficiaries/{beneficiary_id}/commissions/{commission_id}
     */
    public function beneficiaryCommissionEdit(
        string $beneficiaryId,
        string $commissionId,
        ?\DateTimeInterface $willStartAt = null,
        ?float $percentage = null,
        ?int $fixedPart = null,
        ?int $minimum = null
    ): array {
        return $this->exec('PUT', \sprintf('beneficiaries/%s/commissions/%s', $beneficiaryId, $commissionId), [], [
            'will_start_at' => $willStartAt ? $willStartAt->format('Y-m-d H:i:s e') : null,
            'percentage' => $percentage,
            'fixed_part' => $fixedPart,
            'minimum' => $minimum,
        ]);
    }

    /**
     * DELETE /api/v1/beneficiaries/{beneficiary_id}/commissions/{commission_id}
     */
    public function beneficiaryCommissionDelete(string $beneficiaryId, string $commissionId): array
    {
        return $this->exec('DELETE', \sprintf('beneficiaries/%s/commissions/%s', $beneficiaryId, $commissionId));
    }

    /**
     * POST /api/v1/payment/{client_id}/from/card/to/card
     */
    public function paymentFromCardToCard(string $clientId, ?string $fromRefId = null, ?string $fromEan = null, ?string $toRefId = null, ?string $toEan = null, ?string $redirectUrl = null)
    {
        return $this->exec('POST', \sprintf('payment/%s/from/card/to/card', $clientId), [], [
            'from_ref_id' => $fromRefId,
            'from_ean' => $fromEan,
            'to_ref_id' => $toRefId,
            'to_ean' => $toEan,
            'redirect_url' => $redirectUrl
        ]);
    }

    /**
     * POST /api/v1/sbp/check
     */
    public function sbpCheck(
        string $phone,
        ?string $bic = null,
        ?string $bankName = null
    ) {
        return $this->exec('POST', 'sbp/check', [], $this->filterParams([
            'phone' => $phone,
            'bic' => $bic,
            'bank_name' => $bankName,
        ]));
    }

    /**
     * POST /api/v1/sbp/check-async
     */
    public function sbpCheckAsync(
        string $orderId,
        string $phone,
        ?string $bic = null,
        ?string $bankName = null,
        ?string $fio = null
    ) {
        return $this->exec('POST', 'sbp/check-async', [], $this->filterParams([
            'order_id' => $orderId,
            'phone' => $phone,
            'bic' => $bic,
            'bank_name' => $bankName,
            'fio' => $fio,
        ]));
    }

    /**
     * GET /api/v1/sbp/get-check-async-status
     */
    public function sbpGetCheckAsyncStatus(string $id)
    {
        return $this->exec('POST', 'sbp/check-async/status', [], $this->filterParams(['id' => $id]));
    }

    /**
     * POST /api/v1/sbp/payment
     */
    public function sbpPayment(
        string $phone,
        int $amount,
        ?string $bic = null,
        ?string $bankName = null,
        ?string $orderSlug = null,
        ?string $beneficiaryId = null,
        ?array $receiptIds = null
    ) {
        return $this->exec('POST', 'sbp/payment', [], $this->filterParams([
            'phone' => $phone,
            'amount' => $amount,
            'bic' => $bic,
            'bank_name' => $bankName,
            'order_slug' => $orderSlug,
            'beneficiary_id' => $beneficiaryId,
            'receipt_ids' => $receiptIds,
        ]));
    }

    /**
     * POST /api/v1/clients/{client_id}/check-sbp
     */
    public function sbpClientCheck(
        string $clientId,
        ?string $bic = null,
        ?string $bankName = null
    ) {
        return $this->exec('POST', sprintf('clients/%s/check-sbp', $clientId), [], $this->filterParams([
            'bic' => $bic,
            'bank_name' => $bankName,
        ]));
    }

    /**
     * POST /api/v1/clients/{client_id}/check-sbp-async
     */
    public function sbpClientCheckAsync(
        string $clientId,
        ?string $bic = null,
        ?string $bankName = null
    ) {
        return $this->exec('POST', sprintf('clients/%s/check-sbp-async', $clientId), [], $this->filterParams([
            'bic' => $bic,
            'bank_name' => $bankName,
        ]));
    }

    /**
     * GET /api/v1/clients/{client_id}/check-sbp-async/status
     */
    public function sbpGetClientCheckAsyncStatus(
        string $clientId,
        string $id
    ) {
        return $this->exec('POST', sprintf('clients/%s/check-sbp-async/status', $clientId), [], $this->filterParams([
            'id' => $id,
        ]));
    }

    /**
     * POST /api/v1/clients/{client_id}/payment-sbp
     */
    public function sbpClientPayment(
        string $clientId,
        int $amount,
        ?string $bic = null,
        ?string $bankName = null,
        ?string $orderSlug = null,
        ?string $beneficiaryId = null,
        ?array $receiptIds = null
    ) {
        return $this->exec('POST', sprintf('clients/%s/payment-sbp', $clientId), [], $this->filterParams([
            'amount' => $amount,
            'bic' => $bic,
            'bank_name' => $bankName,
            'order_slug' => $orderSlug,
            'beneficiary_id' => $beneficiaryId,
            'receipt_ids' => $receiptIds,
        ]));
    }

    /**
     * POST /api/v1/document-uploader
     * @var StreamInterface|resource|string $contents
     */
    public function uploadDocument($contents)
    {
        $params = [
            [
                'name' => self::DOCUMENT_UPLOAD_FIELD_NAME,
                'contents' => $contents,
                'filename' => 'upload',
            ]
        ];
        return $this->execMultipart('POST', 'document-uploader', $params);
    }

    /**
     * POST /api/v1/beneficiaries/{beneficiary_id}/add-balance-correction
     */
    public function beneficiaryBalanceCorrection(string $beneficiaryId, int $amount) {
        return $this->exec(
            'PUT',
            sprintf('beneficiaries/%s/add-balance-correction', $beneficiaryId),
            [],
            ['amount' => $amount]
        );
    }

    /**
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getPartnerId(): ?string
    {
        return $this->partnerId;
    }

    /**
     * @param string $partnerId
     */
    public function setPartnerId(string $partnerId): void
    {
        $this->partnerId = $partnerId;
    }


    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function exec(string $method, string $path, array $query = [], array $params = [])
    {
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $date = $date->format(DATE_RFC7231);

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

        try {
            $response = $this->guzzle->request($method, $path, [
                'query' => $query,
                'body' => $body,
                'debug' => $this->isDebug,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'TB-Content-SHA256' => trim($hashBody),
                    'Date' => trim($date),
                    'Authorization' => 'TB1-HMAC-SHA256 ' . $this->partnerId . ':' . $signature,
                ]
            ]);
        } catch (\Exception $exception) {
            if ($this->isDebug) {
                if ($exception instanceof GuzzleException) {
                    echo $exception->getResponse()->getBody()->getContents() . PHP_EOL;
                } else {
                    echo $exception->getMessage() . PHP_EOL;
                }
            }

            throw $exception;
        }

        $content = $response->getBody()->getContents(); // json or string?
        return in_array($content[0], ['{', '[']) ? json_decode($content, true) : $content;
    }

    public function execMultipart(string $method, string $path, array $params = [])
    {
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $date = $date->format(DATE_RFC7231);

        $fullPath = parse_url($this->guzzle->getConfig('base_uri') . $path, PHP_URL_PATH);

        $hashBody = hash('sha256', '');

        $headers = array_change_key_case([
            'TB-Content-SHA256' => trim($hashBody),
            'Date' => trim($date),
        ], CASE_LOWER);

        ksort($headers);
        $headerString = [];

        foreach ($headers as $name => $value) {
            $headerString[] = $name . ':' . $value;
        }

        $string = $method . "\n"; // http verb
        $string .= $fullPath . "\n"; // uri
        $string .= "\n"; // query
        $string .= implode("\n", $headerString) . "\n"; // headers
        $string .= $hashBody; // payload

        $signature = hash_hmac('sha256', $string, $this->token);

        try {
            $response = $this->guzzle->request($method, $path, [
                'multipart' => $params,
                'headers' => [
                    'TB-Content-SHA256' => trim($hashBody),
                    'Date' => trim($date),
                    'Authorization' => 'TB1-HMAC-SHA256 ' . $this->partnerId . ':' . $signature,
                ]]);
        } catch (\Exception $exception) {
            if ($this->isDebug) {
                if ($exception instanceof GuzzleException) {
                    echo $exception->getResponse()->getBody()->getContents() . PHP_EOL;
                } else {
                    echo $exception->getMessage() . PHP_EOL;
                }
            }

            throw $exception;
        }

        $content = $response->getBody()->getContents(); // json or string?
        return in_array($content[0], ['{', '[']) ? json_decode($content, true) : $content;
    }

    /**
     * @param array $params
     * @return array
     */
    private function filterParams(array $params): array
    {
        return array_filter($params, function ($value) {
            return $value !== null;
        });
    }
}
