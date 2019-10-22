<?php

namespace App\Http\Controllers\CustomerCenter;

use App\CustomerOrder;
use App\CustomerRecurringOrder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AbccCustomerRecurringOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $customer_recurring_orders = CustomerRecurringOrder::getRecurringOrders();

        $customer_recurring_orders->setPath('recurringorders');

        return view('abcc.recurring_orders.index', compact('customer_recurring_orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $recurring_order = CustomerRecurringOrder::getRecurringOrder($id);
        $customer_orders = CustomerOrder::ofLoggedCustomer()->get()->pluck('document_reference', 'id');

        if (!$recurring_order)
            return redirect()->route('abcc.recurring_orders.index')
                             ->with('error', l('The record with id=:id does not exist',
                                               ['id' => $id], 'layouts'));

        return view('abcc.recurring_orders.edit', compact('recurring_order', 'customer_orders'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int    $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $recurring_order = CustomerRecurringOrder::getRecurringOrder($id);
        $recurring_order->update($request->all());

        return redirect()->route('abcc.recurringorders.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
