<?php

namespace App\Http\Controllers;

use App\Context;
use App\CustomerRecurringOrder;
use App\Sequence;
use App\Traits\DateFormFormatterTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class CustomerRecurringOrderController extends Controller
{

    use DateFormFormatterTrait;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $customer_recurring_orders = CustomerRecurringOrder::getRecurringOrders();

        $customer_recurring_orders->setPath('recurringorders');

        return view('customer_recurring_orders.index', compact('customer_recurring_orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $customer_order_id
     * @return Response
     */
    public function create($customer_order_id)
    {
        $suggested_frequency = 7;

        $recurring_order = new CustomerRecurringOrder();
        $recurring_order->start_at = today();
        $recurring_order->next_at = today()->addDays($suggested_frequency);
        $recurring_order->end_at = today()->addDays($suggested_frequency);
        $recurring_order->frequency = $suggested_frequency;
        $recurring_order->customer_order_id = $customer_order_id;

        return view('customer_recurring_orders.create', compact('recurring_order'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $this->formatDates($request);

        CustomerRecurringOrder::create($data);

        return redirect()->route('recurringorders.index')->with('success', l('Created recurring order'));
    }

    /**
     * @param Request $request
     * @return array
     */
    private function formatDates(Request $request)
    {
        $this->mergeFormDates( ['start_at', 'end_at'], $request );

        $data = $request->all();

        if ($data['start_at']) {
            $data['next_at'] = Carbon::parse($data['start_at'])
                                     ->addDays($data['frequency'])
                                     ->toDateString();
        }
        return $data;
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

        if (!$recurring_order) {
            return redirect()->route('customer_recurring_orders.index')
                             ->with('error', l('The record with id=:id does not exist',
                                               ['id' => $id], 'layouts'));
        }

        return view('customer_recurring_orders.edit', compact('recurring_order'));
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

        $data = $this->formatDates($request);

        $recurring_order->update($data);

        return redirect()->route('recurringorders.index')->with('success', l('Updated recurring order'));
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

        return redirect()->route('recurringorders.index')->with('success', l('Deleted recurring order'));
    }

    public function cron()
    {
        $pending_recurring_orders = CustomerRecurringOrder::getRecurringOrdersForCron();

        /** @var CustomerRecurringOrder $recurring_order */
        foreach ($pending_recurring_orders as $recurring_order) {

            if ($recurring_order->next_at <= today()) {
                // Create order and notify
                $this->createNewCustomerOrder($recurring_order);
                $this->notifyNewCustomerOrder($recurring_order);

                // update next occurrence
                $recurring_order->next_at = today()->addDays($recurring_order->frequency);
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
        $new_order->created_via = 'recurring_order';
        $new_order->document_date = today();

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


            $name = 'emails.' . Context::getContext()->language->iso_code . '.new_customer_order';
            $send = Mail::send($name, $template_vars, function ($message) use ($data) {
                $message->from($data['from'], $data['fromName']);

                $message->to($data['to'], $data['toName'])->bcc($data['from'])->subject($data['subject']);    // Will send blind copy to sender!
            });
        }
        catch (Exception $e) {

            // TODO. change this. notify somehow
            return redirect()->route('recurringorders.index')
                             ->with('error', l('There was an error. Your message could not be sent.', [], 'layouts') . '<br />' .
                                             $e->getMessage());
        }
    }
}
