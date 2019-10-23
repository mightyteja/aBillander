<?php

namespace App\Http\Controllers\CustomerCenter;

use App\CustomerOrder;
use App\CustomerRecurringOrder;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $recurring_order = new CustomerRecurringOrder();
        $recurring_order->start_at = now();
        $recurring_order->next_occurring_at = now();
        $customer_orders = $this->getFormattedCustomerOrdersForDropdown();
        return view('abcc.recurring_orders.create', compact('recurring_order','customer_orders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $data['next_occurring_at'] = Carbon::parse($data['start_at'])
                                           ->addDays($data['frequency'])
                                           ->toDateTimeLocalString();

        $recurring_order = new CustomerRecurringOrder();
        CustomerRecurringOrder::create($data);

        return redirect()->route('abcc.recurringorders.index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $recurring_order = CustomerRecurringOrder::getRecurringOrder($id);
        $customer_orders = $this->getFormattedCustomerOrdersForDropdown();

        if (!$recurring_order) {
            return redirect()->route('abcc.recurring_orders.index')
                             ->with('error', l('The record with id=:id does not exist',
                                               ['id' => $id], 'layouts'));
        }

        return view('abcc.recurring_orders.edit', compact('recurring_order', 'customer_orders'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $recurring_order = CustomerRecurringOrder::getRecurringOrder($id);
        $data = $request->all();

        $data['next_occurring_at'] = Carbon::parse($data['start_at'])
                                           ->addDays($data['frequency'])
                                           ->toDateTimeLocalString();

        $recurring_order->update($data);

        return redirect()->route('abcc.recurringorders.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @return mixed
     */
    private function getFormattedCustomerOrdersForDropdown()
    {
        // custom_order_name alias defined in CustomerOrder
        $customer_orders = CustomerOrder::ofLoggedCustomer()
                                        ->get()
                                        ->pluck('custom_order_name', 'id');
        return $customer_orders;
    }
}
