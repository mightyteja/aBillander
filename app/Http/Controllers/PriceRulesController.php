<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\PriceRule;
use App\Product;
use App\Configuration;

use App\Traits\DateFormFormatterTrait;

class PriceRulesController extends Controller 
{
   
   use DateFormFormatterTrait;

   protected $pricerule;

   public function __construct(PriceRule $pricerule)
   {
        $this->pricerule = $pricerule;
   }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
        // Dates (cuen)
        $this->mergeFormDates( ['date_from', 'date_to'], $request );

        $rules = $this->pricerule
        						->filter( $request->all() )
        						->with('category')
        						->with('product')
        						->with('combination')
        						->with('customer')
        						->with('customergroup')
        						->with('currency')
        						->orderBy('product_id', 'ASC')
        						->orderBy('customer_id', 'ASC')
        						->orderBy('from_quantity', 'ASC');

//         abi_r($mvts->toSql(), true);

        $rules = $rules->paginate( Configuration::get('DEF_ITEMS_PERPAGE') );
        // $mvts = $mvts->paginate( 1 );

        $rules->setPath('pricerules');     // Customize the URI used by the paginator

        $rule_typeList = PriceRule::getRuleTypeList();

        return view('price_rules.index', compact('rules', 'rule_typeList'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('price_rules.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{

        // Dates (cuen)
        $this->mergeFormDates( ['date_from', 'date_to'], $request );

        // Force Currency
        $request->merge( [ 'currency_id' => \App\Context::getContext()->currency->id, 'price' => (float) $request->input('price', 0) ] );

		$rule_type = $request->input('rule_type');
		
		if ($rule_type == 'promo') 
		{
			// If rule_type = 'promo', price maybe null
			$request->merge( ['from_quantity' => $request->input('from_quantity_promo')] );
		}
		
		if ($rule_type == 'pack') 
		{
			// 
			$product_id = $request->input('product_id');
			$measure_unit_id = $request->input('measure_unit_id');

			$product = Product::find($product_id);

			$conversion_rate = $product->extra_measureunits->where('id', $measure_unit_id)->first()->conversion_rate;


			$request->merge( [
								'from_quantity'   => $request->input('from_quantity_pack'),
								'price'           => $request->input('price_pack'),
								'conversion_rate' => $conversion_rate,
							] );
		}

		$this->validate($request, PriceRule::$rules);
		
		$pricerule = $this->pricerule->create($request->all());

		if($request->ajax()){

	        return response()->json( [
                'msg' => 'OK',
                'data' => $pricerule->toArray()
        	] );

	    }

		return redirect('pricerules')
				->with('info', l('This record has been successfully created &#58&#58 (:id) ', ['id' => $pricerule->id], 'layouts') );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$stockmove = $this->pricerule->findOrFail($id);

		return view('price_rules.show', compact('stockmove'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		
        $this->pricerule->find($id)->delete();

		return redirect()->back()
				->with('success', l('This record has been successfully deleted &#58&#58 (:id) ', ['id' => $id], 'layouts'));
	}

}