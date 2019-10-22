<?php

namespace App;

use Carbon\Carbon;

class CustomerRecurringOrder extends Billable
{
    public static $rules = [];

    protected $fillable = ['customer_order_id', 'start_at', 'next_occurring_at', 'frequency', 'active'];
    //protected $dates    = ['start_at', 'next_occurring_at'];


    public function setStartAtAttribute($value)
    {
        $this->attributes['start_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }


    public static function getRecurringOrders()
    {
        return self::with('customerOrder')
                   ->ofLoggedCustomer()  // Of Logged in Customer (see scope on Billable
                   ->with('currency')
                   ->paginate(Configuration::get('ABCC_ITEMS_PERPAGE'));

    }

    public static function getRecurringOrder($id)
    {
        return self::with('customerOrder')
                   ->ofLoggedCustomer()  // Of Logged in Customer (see scope on Billable
                   ->find($id);
    }

    public function customerOrder()
    {
        return $this->belongsTo('App\CustomerOrder')->with('currency');
    }
}
