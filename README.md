# api-baas-php
Bank as a service by TalkBank

`composer require talkbank-io/api-baas-php`

## Api methods
### Account methods
 * GET /balance
 * GET /operations
 * GET /operations/{type}/{id}
 * GET /cards-transactions
### Card methods
 * GET /clients/{client_id}/cards/{barcode}/transactions
 * GET /clients/{client_id}/cards
 * GET /clients/{client_id}/cards/{barcode}
 * GET /clients/{client_id}/cards/{barcode}/{order_id}
 * GET /clients/{client_id}/cards/{barcode}/balance
 * GET /clients/{client_id}/cards/{barcode}/lock
 * POST /clients/{client_id}/cards/{barcode}/lock
 * DELETE /clients/{client_id}/cards/{barcode}/lock
 * POST /clients/{client_id}/virtual-cards
 * POST /clients/{client_id}/cards/{barcode}/activate
 * GET /clients/{client_id}/cards/{barcode}/activation
 * GET /clients/{client_id}/cards/{barcode}/security-code
 * GET /clients/{client_id}/cards/{barcode}/cardholder/data
 * GET /clients/{client_id}/cards/{barcode}/limits
 * POST /clients/{client_id}/cards/{barcode}/refill
 * POST /clients/{client_id}/cards/{barcode}/withdrawal
 * POST /clients/{client_id}/cards/{barcode}/set/pin
 * GET /clients/{client_id}/cards/{barcode}/pdf
 * POST /clients/{client_id}/cards/{barcode}/limits
### Event Subscription Methods
 * GET /event-subscriptions
 * POST /event-subscriptions
 * DELETE /event-subscriptions
### Delivery methods
 *  POST /clients/{client_id}/card-deliveries
 *  GET /clients​/{client_id}​/card-deliveries​/{delivery_id}
### Client Methods
 * POST /clients
 * PUT /clients/{client_id}
 * GET /clients/{client_id}
### Hold
 * POST /hold
 * POST /hold/{client_id}/with/form
 * POST /hold/confirm/{order_slug}
 * POST /hold/reverse/{order_slug}
### Payment 
 * POST /charge/{client_id}/unregistered/card
 * POST /charge/{client_id}/token
 * POST /refill/{client_id}/token
 * POST /charge/{client_id}/unregistered/card/with/form
 * POST /payment/from/{client_id}/registered/card
 * POST /authorize/card/{client_id}
 * POST /authorize/card/{client_id}/token
 * POST /authorize/card/{client_id}/with/form
 * POST /payment/to/{client_id}/registered/card
 * POST /account/transfer
 * POST /refill/unregistered/card
 * POST /refill/{client_id}/unregistered/card/with/form
 * GET /payment/{order_slug}
 * GET /api/v1/payment/{order_slug}/receipt
 * POST /api/v1/payment/{client_id}/from/card/to/card
 * POST /sbp/check
 * POST /sbp/payment
 * POST /clients/{client_id}/check-sbp
 * POST /clients/{client_id}/payment-sbp
### Self-employment's Methods
 * GET /selfemployments/{client_id}
 * GET /selfemployments/{client_id}/income_reference
 * GET /selfemployments/{client_id}/registration_reference
 * GET /selfemployments/{client_id}/account_status
 * GET /selfemployments/{client_id}/income
 * POST /selfemployments/{client_id}/bind
 * POST /selfemployments/{client_id}/check_bind
 * POST /selfemployments/{client_id}/receipt-async
### Clients
 * POST /client/v1/charge
 * POST /client/v1/refill
 * POST /client/v1/authorize
 * POST /client/v1/hold
 * GET /client/v1/status/{hash}
### Marketplace
* POST /marketplace/itelier/order
* POST /marketplace/itelier/atelier
# Beneficiaries
* GET /api/v1/beneficiaries
* POST /api/v1/beneficiaries
* PUT /api/v1/beneficiaries/{beneficiary_id}
* GET /api/v1/beneficiaries/{beneficiary_id}
* PUT /api/v1/beneficiaries/{beneficiary_id}/add-balance-correction
* GET /api/v1/beneficiaries/{beneficiary_id}/commissions
* POST /api/v1/beneficiaries/{beneficiary_id}/commissions
* PUT /api/v1/beneficiaries/{beneficiary_id}/commissions/{commission_id}
* DELETE /api/v1/beneficiaries/{beneficiary_id}/commissions/{commission_id}

## Unit tests
Run tests: `php bin/phpunit tests/ApiClientTest.php`
