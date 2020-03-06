# api-baas-php
Bank as a service by TalkBank

## Api methods
### Account methods
 * GET /balance
 * GET /transactions
 * GET /cards-transactions
### Card methods
 * GET /clients/{client_id}/cards/{barcode}/transactions
 * GET /clients/{client_id}/cards
 * GET /clients/{client_id}/card
 * GET /clients/{client_id}/cards/{barcode}/{order_id}
 * GET /clients/{client_id}/cards/{barcode}/balance
 * POST /clients/{client_id}/cards/{barcode}/lock
 * DELETE /clients/{client_id}/cards/{barcode}/lock
 * POST /clients/{client_id}/virtual-cards
 * POST /clients/{client_id}/cards/{barcode}/activate
 * GET /clients/{client_id}/cards/{barcode}/activation
 * GET /clients/{client_id}/cards/{barcode}/security-code
 * GET /clients/{client_id}/cards/{barcode}/cardholder/data
 * POST /clients/{client_id}/cards/{barcode}/refill
 * POST /clients/{client_id}/cards/{barcode}/withdrawal
 * POST /clients/{client_id}/cards/{barcode}/set/pin
 * GET /clients/{client_id}/pdf
### Event Subscription Methods
 * GET /event-subscriptions
 * POST /event-subscriptions
 * DELETE /event-subscriptions
### default
 * GET /clients/{client_id}/cards/{barcode}/cardholder/data
 * GET /clients/{client_id}/cards/{barcode}/limits
### Delivery methods
 *  POST /clients/{client_id}/card-deliveries
 *  GET /clients/{client_id}/card-deliveries
### Client Methods
 * POST /clients
 * GET /clients/{client_id}
 * PUT /clients/{client_id}
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
### Self-employment's Methods
 * GET /selfemployments/{client_id}
### Clients
 * POST /client/v1/charge
 * POST /client/v1/refill
 * POST /client/v1/authorize
 * POST /client/v1/hold
 * GET /client/v1/status/{hash}
 