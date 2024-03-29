<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Customer;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

use App\Http\Resources\V1\CustomerResource;
use App\Http\Resources\V1\CustomerCollection;
use App\Filters\V1\CustomersFilter;
use App\Http\Requests\V1\StoreCustomerRequest;
use App\Http\Requests\V1\UpdateCustomerRequest;
use App\Http\Requests\V1\DeleteCustomerRequest;
use Illuminate\Http\JsonResponse;
use PhpOption\None;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new CustomersFilter();
        $filterItems = $filter->transform($request);

        $includeInvoices = $request->query('includeInvoices');
        $name = $request->query('name');

        $orderBy = $request->query('orderBy');
        $sortOrder = $request->query('sortOrder');

        $orderBy2 = $request->query('orderBy2');
        $sortOrder2 = $request->query('sortOrder2');

        $customers = Customer::where($filterItems);

        if ($name) {
            $customers = $customers->where('name', 'like', '%' . $name . '%');
        }

        if ($includeInvoices) {
            $customers = $customers->with('invoices');
        }

        if ($orderBy && $sortOrder) {
            $customers = $customers->orderBy($orderBy, $sortOrder);
        }

        if ($orderBy2 && $sortOrder2) {
            $customers = $customers->orderBy($orderBy2, $sortOrder2);
        }

        return new CustomerCollection($customers->paginate()->appends($request->query()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $includeInvoices= request()->query('includeInvoices');

        if($includeInvoices){
            return new CustomerResource($customer->loadMissing('invoices'));
        }

        return new CustomerResource($customer);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->all());

        return response()->json([
            'message'=> 'created',
            'data'=> $customer,
            'errors'=> null,
        ]);
    //    return new CustomerResource(Customer::create($request->all()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->all());

        return response()->json([
            'message'=> 'created',
            'data'=> $customer,
            'errors'=> null,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteCustomerRequest $request, Customer $customer)
    {
        if ($request->authorize()) {
            $customer->delete();
            return response()->json(['message' => 'Customer deleted successfully'], 200);
        }

        return null;
    }
}
