<?php

namespace App;

use App\Traits\ViewFormatterTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CustomerRecurringOrder extends Model
{
    use ViewFormatterTrait;

    public static $rules = [];

    protected $fillable = ['name', 'customer_order_id', 'start_at', 'next_at', 'end_at', 'frequency', 'notes', 'active'];


    public static function getRecurringOrders()
    {
        return self::with('customerOrder')
                   ->ofLoggedCustomer()  // Of Logged in Customer (see scope on Billable
                   ->paginate(Configuration::get('ABCC_ITEMS_PERPAGE'));

    }

    public static function getRecurringOrdersForCron()
    {
        return self::with('customerOrder')
                   ->ofLoggedCustomer()
                   ->where('active', 1)
                   ->get();

    }

    public static function getRecurringOrder($id)
    {
        return self::with('customerOrder')
                   ->ofLoggedCustomer() // Of Logged in Customer
                   ->find($id);
    }

    public function customerOrder()
    {
        return $this->belongsTo('App\CustomerOrder')->with('currency');
    }


    public function scopeOfLoggedCustomer($query)
    {
        if (Auth::guard('customer')->check() &&
            (Auth::guard('customer')->user()->customer_id != null)) {

            $customer_id = Auth::guard('customer')->user()->customer_id;

            $query->whereHas('customerOrder', function ($query) use ($customer_id) {
                return $query->where('customer_id', $customer_id);
            });

            return $query;
        }

        // Not allow to see resource
        return $query->where('customer_id', 0)->where('status', '');
    }
}
