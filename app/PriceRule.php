<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ViewFormatterTrait;

class PriceRule extends Model
{
    use ViewFormatterTrait;

    public static $types = [
                'price', 
                'promo',       // Extra units free of charge
                'pack',        // Price for different measure unit than default / stock measure unit
            ];

    protected $dates = [
        'date_from',
        'date_to',
    ];

    protected $fillable = ['name', 'category_id', 'product_id', 'combination_id',
                           'customer_id', 'customer_group_id',
                           'currency_id', 'rule_type', 'discount_type',
                           'price', 'discount_percent', 'discount_amount', 'discount_amount_is_tax_incl',
                           'from_quantity', 'extra_quantity',
                           'date_from', 'date_to',
                           'measure_unit_id', 'conversion_rate',
    ];

    public static $rules = [
        'category_id'       => 'nullable|exists:categories,id',
        'product_id'        => 'nullable|exists:products,id',
        'combination_id'    => 'nullable|exists:combinations,id',
        'customer_id'       => 'nullable|exists:customers,id',
        'customer_group_id' => 'nullable|exists:customer_groups,id',
        'currency_id'       => 'nullable|exists:currencies,id',
//        'measure_unit_id'   => 'nullable|exists:measureunits,id',
        'date_from'         => 'nullable|date',
        'date_to'           => 'nullable|date',
        'from_quantity'     => 'numeric|min:0',
        'extra_quantity'    => 'numeric|min:0',

        'price'    => 'numeric|min:0',
    ];


    public static function getRuleTypeList()
    {
            $list = [];
            foreach (static::$types as $type) {
                // $list[$scheme] = l(get_called_class().'.'.$scheme, 'sepasp');
                $list[$type] = $type;
            }

            return $list;
    }


    /**
     * Handy method
     *
     * @param Product $product
     */
    public function removeLine(Product $product)
    {
        $line = $this->pricelistlines()->where('product_id', '=', $product->id)->first();
        $line->delete();
    }

    /**
     * Return true or false if a rule applies to a product right now
     *
     * @param $qty
     * @return bool
     */
    public function applies($qty)
    {
        $now = Carbon::now();
        if ($this->from_quantity <= $qty &&
            (is_null($this->date_from) || $this->date_from <= $now) &&
            (is_null($this->date_to) || $this->date_to >= $now)) {

            return true;
        }
        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function combination()
    {
        return $this->belongsTo('App\Combination');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function customergroup()
    {
        return $this->belongsTo('App\CustomerGroup', 'customer_group_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function measureunit()
    {
        return $this->belongsTo('App\MeasureUnit', 'measure_unit_id');
    }


    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeFilter($query, $params)
    {

        if ($params['date_from']) // if ( isset($params['date_to']) && trim($params['date_to']) != '' )
        {
            $query->where('date', '>=', $params['date_from'] . ' 00:00:00');
        }

        if ($params['date_to']) {
            $query->where('date', '<=', $params['date_to'] . ' 23:59:59');
        }


        if (isset($params['reference']) && trim($params['reference']) !== '')
        {
            $stub = $params['reference'];

            $query->whereHas('product', function($q) use ($stub) 
            {
                $q->where('reference', 'LIKE', '%' . $stub . '%');

            });
        }

        if (isset($params['name']) && trim($params['name']) !== '')
        {
            $stub = $params['name'];

            $query->whereHas('product', function($q) use ($stub) 
            {
                $q->where('name', 'LIKE', '%' . $stub . '%');

            });
        }

        if (isset($params['rule_type']) && in_array($params['rule_type'], static::$types)) {
            $query->where('rule_type', '=', $params['rule_type']);
        }

        return $query;
    }

}