<?php

namespace App\Http\Controllers\CustomerCenter;

use App\Context;
use App\CustomerOrder;
use App\CustomerRecurringOrder;
use App\Http\Controllers\Controller;
use App\Sequence;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

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

        return view('abcc.recurring_orders.create', compact('recurring_order', 'customer_orders'));
    }

    /**
     * @return mixed
     */
    private function getFormattedCustomerOrdersForDropdown()
    {
        // custom_order_name alias defined in CustomerOrder
        return CustomerOrder::ofLoggedCustomer()
                            ->get()
                            ->pluck('custom_order_name', 'id');
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

        CustomerRecurringOrder::create($data);

        return redirect()->route('abcc.recurringorders.index')->with('success', l('Created recurring order'));
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

        return redirect()->route('abcc.recurringorders.index')->with('success', l('Updated recurring order'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $recurring_order = CustomerRecurringOrder::getRecurringOrder($id);
        $recurring_order->delete();

        return redirect()->route('abcc.recurringorders.index')->with('success', l('Deleted recurring order'));
    }

    public function cron()
    {
        $pending_recurring_orders = CustomerRecurringOrder::getRecurringOrdersForCron();

        /** @var CustomerRecurringOrder $recurring_order */
        foreach ($pending_recurring_orders as $recurring_order) {

            if ($recurring_order->next_occurring_at <= now()) {
                // Create order and notify
                $this->createNewCustomerOrder($recurring_order);
                $this->notifyNewCustomerOrder($recurring_order);

                // update next occurrence
                $recurring_order->next_occurring_at = now()->addDays($recurring_order->frequency);
                $recurring_order->save();

                echo 'Created new order from recurring order: ' . $recurring_order->id;
            } else {
                echo 'Nothing to to with recurring order: ' . $recurring_order->id . '<br />';
            }
        }
    }

    /**
     * @param $recurring_order
     */
    private function createNewCustomerOrder($recurring_order)
    {
        $new_order = $recurring_order->customerOrder->replicate();

        // minor changes
        $new_order->created_via = 'cron'; // ??
        $new_order->document_date = now();

        $seq = Sequence::find($new_order->sequence_id);
        $doc_id = $seq->getNextDocumentId();

        $new_order->document_prefix = $seq->prefix;
        $new_order->document_id = $doc_id;
        $new_order->document_reference = $seq->getDocumentReference($doc_id);

        $new_order->status = 'confirmed';
        $new_order->validation_date = now();

        $new_order->save();
    }

    /**
     * @param $recurring_order
     * @return RedirectResponse
     */
    private function notifyNewCustomerOrder($recurring_order)
    {
        try {

            $template_vars = [
                'document_num'   => $recurring_order->document_reference,
                'document_date'  => abi_date_short($recurring_order->document_date),
                'document_total' => $recurring_order->as_money('total_tax_excl'),
            ];

            $data = [
                'from'     => abi_mail_from_address(),
                'fromName' => abi_mail_from_name(),
                'to'       => abi_mail_from_address(),
                'toName'   => abi_mail_from_name(),
                'subject'  => l(' :_> New Customer Order #:num', ['num' => $template_vars['document_num']]),
            ];


            $name = 'emails.' . Context::getContext()->language->iso_code . '.abcc.new_customer_order';
            $send = Mail::send($name, $template_vars, function ($message) use ($data) {
                $message->from($data['from'], $data['fromName']);

                $message->to($data['to'], $data['toName'])->bcc($data['from'])->subject($data['subject']);    // Will send blind copy to sender!
            });
        }
        catch (Exception $e) {

            // TODO. change this. notify somehow
            return redirect()->route('abcc.recurringorders.index')
                             ->with('error', l('There was an error. Your message could not be sent.', [], 'layouts') . '<br />' .
                                             $e->getMessage());
        }
    }
}
