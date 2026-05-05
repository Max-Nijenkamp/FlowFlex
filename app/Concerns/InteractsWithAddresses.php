<?php

namespace App\Concerns;

use App\Models\Address;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait InteractsWithAddresses
{
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function primaryAddress(): ?Address
    {
        return $this->addresses()->where('is_primary', true)->first()
            ?? $this->addresses()->latest()->first();
    }

    public function addAddress(array $attributes): Address
    {
        if ($attributes['is_primary'] ?? false) {
            $this->addresses()->update(['is_primary' => false]);
        }

        return $this->addresses()->create($attributes);
    }

    public function updateAddress(Address $address, array $attributes): Address
    {
        if ($attributes['is_primary'] ?? false) {
            $this->addresses()->where('id', '!=', $address->id)->update(['is_primary' => false]);
        }

        $address->update($attributes);

        return $address->fresh();
    }

    public function removeAddress(Address $address): bool
    {
        return $address->delete();
    }
}
