<?php

namespace App\Contracts;

use App\Models\Address;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasAddresses
{
    public function addresses(): MorphMany;

    public function primaryAddress(): ?Address;

    public function addAddress(array $attributes): Address;

    public function updateAddress(Address $address, array $attributes): Address;

    public function removeAddress(Address $address): bool;
}
