<?php

// Copyright (C) 2024 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Opticash\Interfaces;

use BrokeYourBike\Opticash\Enums\PaymentMethodEnum;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
interface NameEnquiryInterface
{
    public function getCurrencyId(): int;
    public function getRecipientIdentifier(): string;
    public function getRecipientInstitutionId(): string;
}
