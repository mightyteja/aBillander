<?php 

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\ShippingMethod;
use View;

class ShippingMethodsController extends Controller {


   protected $shippingmethod;

   public function __construct(ShippingMethod $shippingmethod)
   {
        $this->shippingmethod = $shippingmethod;
   }

	/**
	 * Display a listing of the resource.
	 * GET /shippingmethods
	 *
	 * @return Response
	 */
	public function index()
	{
		$shippingmethods = $this->shippingmethod
						->with('carrier')
						->orderBy('carrier_id', 'asc')
						->orderBy('name', 'asc')
						->get();

        return view('shipping_methods.index', compact('shippingmethods'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /shippingmethods/create
	 *
	 * @return Response
	 */
	public function create()
	{
		$shippingmethod = $this->shippingmethod;
		$shippingmethod->carrier_id = null;

		return view('shipping_methods.create', compact('shippingmethod'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /shippingmethods
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, ShippingMethod::$rules);

		$shippingmethod = $this->shippingmethod->create($request->all());

		if ( ShippingMethod::count() == 1 )
			\App\Configuration::updateValue('DEF_CUSTOMER_PAYMENT_METHOD', $shippingmethod->id);

		return redirect('shippingmethods')
				->with('info', l('This record has been successfully created &#58&#58 (:id) ', ['id' => $shippingmethod->id], 'layouts') . $request->input('name'));
	}

	/**
	 * Display the specified resource.
	 * GET /shippingmethods/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		// Temporarily:
		return $this->edit($id);
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /shippingmethods/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$shippingmethod = $this->shippingmethod->findOrFail($id);
		
		return view('shipping_methods.edit', compact('shippingmethod'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /shippingmethods/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		$shippingmethod = $this->shippingmethod->findOrFail($id);

		$this->validate($request, ShippingMethod::$rules);

		$shippingmethod->update($request->all());

		return redirect('shippingmethods')
				->with('success', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $id], 'layouts') . $request->input('name'));
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /shippingmethods/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $this->shippingmethod->findOrFail($id)->delete();

        return redirect('shippingmethods')
				->with('success', l('This record has been successfully deleted &#58&#58 (:id) ', ['id' => $id], 'layouts'));
	}

}