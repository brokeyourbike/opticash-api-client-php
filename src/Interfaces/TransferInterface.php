<?php

// Copyright (C) 2024 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Opticash\Interfaces;

use BrokeYourBike\Opticash\Enums\PaymentMethodEnum;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
interface TransferInterface
{
    public function getPaymentMethod(): PaymentMethodEnum;
    public function getAmount(): float;
    public function getCurrency(): string;
    public function getCurrencyId(): int;
    public function getReference(): string;
    public function getSenderName(): string;
    public function getRecipientName(): string;
    public function getRecipientIdentifier(): string;
    public function getRecipientInstitution(): string;
    public function getRecipientInstitutionId(): string;
}
