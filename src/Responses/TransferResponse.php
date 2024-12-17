<?php

// Copyright (C) 2024 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Opticash\Responses;

use Spatie\DataTransferObject\Attributes\MapFrom;
use BrokeYourBike\DataTransferObject\JsonResponse;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class TransferResponse extends JsonResponse
{
    public ?string $status;
    public ?string $message;

    #[MapFrom('data.reference')]
    public ?string $reference;

    #[MapFrom('data.customer_reference')]
    public ?string $customerReference;

    #[MapFrom('data.status')]
    public ?string $transactionStatus;
}
