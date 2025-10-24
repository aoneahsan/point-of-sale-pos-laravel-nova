<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('group');
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->search($search);
        }
        
        if ($request->has('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        
        $customers = $query->paginate($request->get('per_page', 15));
        
        return CustomerResource::collection($customers);
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer->load('group'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
        ]);

        $validated['code'] = 'CUST-' . str_pad(Customer::count() + 1, 6, '0', STR_PAD_LEFT);
        
        $customer = Customer::create($validated);
        return new CustomerResource($customer);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'sometimes|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable',
        ]);

        $customer->update($validated);
        return new CustomerResource($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}