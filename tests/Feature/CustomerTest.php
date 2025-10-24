<?php

use App\Models\Customer;
use App\Models\Store;
use App\Models\CustomerGroup;

test('can add loyalty points', function () {
    $customer = Customer::factory()->create(['loyalty_points' => 0]);

    $customer->addLoyaltyPoints(100);

    expect($customer->fresh()->loyalty_points)->toBe(100);
});

test('can add store credit', function () {
    $customer = Customer::factory()->create(['store_credit' => 0]);

    $customer->addStoreCredit(50.00);

    expect($customer->fresh()->store_credit)->toBe(50.00);
});

test('customer belongs to group', function () {
    $group = CustomerGroup::factory()->create();
    $customer = Customer::factory()->create(['customer_group_id' => $group->id]);

    expect($customer->group)->toBeInstanceOf(CustomerGroup::class)
        ->and($customer->group->id)->toBe($group->id);
});