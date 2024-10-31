<?php

// Copyright (C) 2024 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Opticash\Tests;

use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\Opticash\Responses\TransferResponse;
use BrokeYourBike\Opticash\Interfaces\TransferInterface;
use BrokeYourBike\Opticash\Interfaces\ConfigInterface;
use BrokeYourBike\Opticash\Enums\RequestStatusEnum;
use BrokeYourBike\Opticash\Client;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class TransferStatusTest extends TestCase
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
                "status": "error",
                "message": "string",
                "meta": { }
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->once()->andReturn($mockedResponse);

        /**
         * @var ConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($mockedConfig, $mockedClient);

        $requestResult = $api->status('ref-123');
        $this->assertInstanceOf(TransferResponse::class, $requestResult);
        $this->assertEquals(RequestStatusEnum::ERROR->value, $requestResult->status);
    }
}
