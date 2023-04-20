# Changelog
Used [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) and [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v1.35.0] - 2023-04-20
### Added
- Method `sbpCheckAsync`
- Method `sbpGetCheckAsyncStatus`
- Method `sbpClientCheckAsync`
- Method `sbpClientGetCheckAsyncStatus`

## [v1.34.0] - 2022-09-09
### Added
- Method `beneficiaryBalanceCorrection`

## [v1.33.0] - 2022-08-26
### Changed
- Updated signature of method sbpPayment

## [v1.32.0] - 2022-08-17
### Added
- Method `uploadDocument`

## [v1.31.0] - 2022-07-22
### Added
- Method `sbpCheck`
- Method `sbpPayment`
- Method `sbpClientCheck`
- Method `sbpClientPayment`

## [v1.30.0] - 2022-06-17
### Changed
- Added parameters filtering to method registerReceiptAsync

## [v1.29.0] - 2022-05-19
### Updated
- Updated signature of method: paymentToAccountWithTaxData

## [v1.28.0] - 2022-05-05
### Updated
- Added incomeCode to paymentToAccount

## [v1.27.0] - 2022-04-18
### Added
- Method paymentFromCardToCard

## [v1.26.0] - 2022-04-05
### Updated
- Updated signature of methods: cardRefill, paymentToRegisteredCard, paymentToAccountWithTaxData, paymentToUnregisteredCard, paymentToUnregisteredCardWithForm

## [v1.25.0] - 2022-04-01
### Updated
- Updated signature of method paymentToAccount

## [v1.24.0] - 2021-12-06
### Updated
- Change construct for make without auth data
### Added
- Add getter/setter for partnerId and token property

## [v1.23.0] - 2021-11-29
### Added
- Method registerReceiptAsync

## [v1.22.0] - 2021-11-08
### Updated
- Updated signature of method itelierCreateOrder

## [v1.21.0] - 2021-11-02
### Added
- Method selfemploymentsIncomeReference
- Method selfemploymentsRegistrationReference
- Method selfemploymentsAccountStatus
- Method selfemploymentsIncome

## [v1.20.0] - 2021-10-14
### Removed
- Parameter $beneficiaryId in method paymentToAccountWithTaxData

## [v1.19.0] - 2021-10-12
### Added
- Methods beneficiaryCommissionList, beneficiaryCommissionAdd, beneficiaryCommissionEdit, beneficiaryCommissionDelete
### Removed
- Parameter $commissionProc in method beneficiaryAdd

## [v1.18.0] - 2021-10-08
### Added
- Methods beneficiaryList, beneficiaryAdd, beneficiaryEdit, beneficiaryBlock, beneficiaryUnblock, beneficiaryShow
- Parameter $beneficiaryId in methods cardRefill, paymentToRegisteredCard, paymentToAccount, paymentToAccountWithTaxData, paymentToUnregisteredCard, paymentToUnregisteredCardWithForm

## [v1.17.0] - 2021-10-05
### Added
- Method bindSelfemployments
- Method checkBindSelfemployments

## [v1.16.0] - 2021-09-10
### Added 
- Method paymentReceipt

## [v1.15.0] - 2021-08-18
### Added 
- Method paymentToAccountWithTaxData

## [v1.14.0] - 2021-08-17
### Added 
- Method itelierCreateAtelier for creating atelier in itelier marketplace
