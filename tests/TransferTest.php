<?php

// Copyright (C) 2024 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Opticash\Tests;

use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\Opticash\Responses\TransferResponse;
use BrokeYourBike\Opticash\Interfaces\TransferInterface;
use BrokeYourBike\Opticash\Interfaces\ConfigInterface;
use BrokeYourBike\Opticash\Enums\TransactionStatusEnum;
use BrokeYourBike\Opticash\Enums\RequestStatusEnum;
use BrokeYourBike\Opticash\Client;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class TransferTest extends TestCase
{
    /** @test */
    public function it_can_prepare_request(): void
    {
        $transaction = $this->getMockBuilder(TransferInterface::class)->getMock();

        /** @var TransferInterface $transaction */
        $this->assertInstanceOf(TransferInterface::class, $transaction);

        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $mockedConfig->method('getUrl')->willReturn('https://api.example/');

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(200);
        $mockedResponse->method('getBody')
            ->willReturn('{
                "status": "success",
                "data": {
                    "amount": 0,
                    "fee": 0,
                    "type": "string",
                    "status": "PENDING",
                    "sender_currency_slug": "string",
                    "receiver_currency_slug": "string",
                    "sender_currency_logo_url": "string",
                    "receiver_currency_logo_url": "string",
                    "payment_details": {
                    "sender": {
                        "email": "string",
                        "user_id": 0,
                        "full_name": "string",
                        "user_name": "string",
                        "name": "string",
                        "account_number": "string",
                        "bank_name": "string",
                        "sort_code": "string",
                        "swift_code": "string",
                        "bank_address": "string",
                        "country": "string",
                        "currency_id": 7,
                        "Amount": 0,
                        "identifier": "string",
                        "identifier_name": "string",
                        "institution_id": "string",
                        "currency_slug": "string",
                        "institution": "string"
                    },
                    "receiver": {
                        "email": "string",
                        "user_id": 0,
                        "full_name": "string",
                        "user_name": "string",
                        "name": "string",
                        "account_number": "string",
                        "bank_name": "string",
                        "sort_code": "string",
                        "swift_code": "string",
                        "bank_address": "string",
                        "country": "string",
                        "currency_id": 7,
                        "Amount": 0,
                        "identifier": "string",
                        "identifier_name": "string",
                        "institution_id": "string",
                        "currency_slug": "string",
                        "institution": "string"
                    },
                    "rate": {
                        "base": "string",
                        "rate": "string",
                        "quote": "string",
                        "rateString": "string",
                        "processing_fee": "string"
                    },
                    "quote_currency": "string",
                    "base_currency": "string",
                    "converted_amount": 0,
                    "source_amount": 0
                    },
                    "created_at": "1970-01-01T00:00:00.000Z",
                    "state": "string",
                    "reference": "ref-123",
                    "message": "string",
                    "timeStamp": "1970-01-01T00:00:00.000Z",
                    "flow": "OUT"
                },
                "meta": {}
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->once()->andReturn($mockedResponse);

        /**
         * @var ConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($mockedConfig, $mockedClient);

        $requestResult = $api->externalTransfer($transaction, 'reference');
        $this->assertInstanceOf(TransferResponse::class, $requestResult);
        $this->assertEquals(RequestStatusEnum::SUCCESS->value, $requestResult->status);
        $this->assertEquals(TransactionStatusEnum::PENDING->value, $requestResult->transactionStatus);
    }
}
