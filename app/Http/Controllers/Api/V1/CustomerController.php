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
        $filterItems= $filter->transform($request); //[['column', 'operator', 'value']]

        $includeInvoices= $request->query('includeInvoices');

        $customers= Customer::where($filterItems);

        if ($includeInvoices) {
            $customers = $customers->with('invoices');
        }

        //->paginate()->appends($request->query()), se quiser paginar a api, coloque na frente de customers;
        return new CustomerCollection($customers->get());
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
    }
}
