<?php

// Copyright (C) 2024 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Opticash;

use GuzzleHttp\ClientInterface;
use BrokeYourBike\ResolveUri\ResolveUriTrait;
use BrokeYourBike\Opticash\Responses\TransferResponse;
use BrokeYourBike\Opticash\Responses\NameEnquiryResponse;
use BrokeYourBike\Opticash\Interfaces\TransferInterface;
use BrokeYourBike\Opticash\Interfaces\NameEnquiryInterface;
use BrokeYourBike\Opticash\Interfaces\ConfigInterface;
use BrokeYourBike\Opticash\Enums\PaymentMethodEnum;
use BrokeYourBike\HttpEnums\HttpMethodEnum;
use BrokeYourBike\HttpClient\HttpClientTrait;
use BrokeYourBike\HttpClient\HttpClientInterface;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\HasSourceModel\HasSourceModelTrait;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class Client implements HttpClientInterface
{
    use HttpClientTrait;
    use ResolveUriTrait;
    use HasSourceModelTrait;

    private ConfigInterface $config;

    public function __construct(ConfigInterface $config, ClientInterface $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function nameEnquiry(NameEnquiryInterface $transaction): NameEnquiryResponse
    {
        $options = [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->config->getToken(),
            ],
            \GuzzleHttp\RequestOptions::JSON => [
                'currency_id' => $transaction->getCurrencyId(),
                'institution_id' => $transaction->getRecipientInstitutionId(),
                'payment_method_id' => PaymentMethodEnum::BANK_TRANSFER->value,
                'identifier' => $transaction->getRecipientIdentifier(),
            ],
        ];

        if ($transaction instanceof SourceModelInterface){
            $options[\BrokeYourBike\HasSourceModel\Enums\RequestOptions::SOURCE_MODEL] = $transaction;
        }

        $response = $this->httpClient->request(
            HttpMethodEnum::POST->value,
            (string) $this->resolveUriFor(rtrim($this->config->getUrl(), '/'), '/api/v1/payment/name-enquiry'),
            $options
        );

        return new NameEnquiryResponse($response);
    }

    public function externalTransfer(TransferInterface $transaction): TransferResponse
    {
        $options = [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->config->getToken(),
            ],
            \GuzzleHttp\RequestOptions::JSON => [
                'amount' => $transaction->getAmount(),
                'recipient_amount' => $transaction->getAmount(),
                'base_currency_id' => $transaction->getCurrencyId(),
                'quote_currency_id' => $transaction->getCurrencyId(),
                'reason' => $transaction->getReference(),
                'payment_method' => $transaction->getPaymentMethod()->value,
                'customerReference' => $transaction->getReference(),
                'senderName' => $transaction->getSenderName(),
                'beneficiary_details' => [
                    'identifier' => $transaction->getRecipientAccountIdentifier(),
                    'identifier_name' => $transaction->getRecipientName(),
                    'institution' => $transaction->getRecipientAccountProvider(),
                    'institution_id' => $transaction->getRecipientAccountProvider(),
                    'currency_slug' => $transaction->getCurrency(),
                ],
                'conversion' => false,
                'saveBeneficiaries' => false,
            ],
        ];

        if ($transaction instanceof SourceModelInterface){
            $options[\BrokeYourBike\HasSourceModel\Enums\RequestOptions::SOURCE_MODEL] = $transaction;
        }

        $response = $this->httpClient->request(
            HttpMethodEnum::POST->value,
            (string) $this->resolveUriFor(rtrim($this->config->getUrl(), '/'), '/api/v1/payouts/external-transfer'),
            $options
        );

        return new TransferResponse($response);
    }

    public function status(string $reference): TransferResponse
    {
        $options = [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->config->getToken(),
            ],
        ];

        $response = $this->httpClient->request(
            HttpMethodEnum::GET->value,
            (string) $this->resolveUriFor(rtrim($this->config->getUrl(), '/'), "/api/v1/transactions/{$reference}/reference"),
            $options
        );

        return new TransferResponse($response);
    }
}
