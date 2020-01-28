<?php

// Most suitable way to go about this is listen to db queries. You can do
/*
\DB::listen(function ($query) {
    dump($query->sql);
    dump($query->bindings);
    dump($query->time);
});
*/

/*
|--------------------------------------------------------------------------
| Gorrino Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




/* ********************************************************** */


Route::get('gnano', function( )
{
	$dt = \Carbon\Carbon::createSafe(2019, 12, 20, 0, 0, 0);

	abi_r($dt);

	$mvts = \App\StockMovement::where('movement_type_id', \App\StockMovement::PURCHASE_ORDER)
                     ->orderBy('date', 'desc')
                     ->get();

    foreach ($mvts as $mvt){

          abi_r($mvt->date.' - '.$mvt->created_at.' - '.$mvt->product_id.' - '.$mvt->movement_type_id.' - '.$mvt->quantity_before_movement.' - '.$mvt->quantity.' - '.$mvt->quantity_after_movement.'<br>');
        }
});


Route::get('nanocart', function( )
{
	$d =  \App\Cart::find(8);

	abi_r($d->shippingaddress->getShippingMethod());
});




/* ********************************************************** */


Route::get('nano', function( )
{
	$d =  \App\Customer::where( 'reference_external', '<>', '' )->first();

	abi_r(class_basename(get_class($d)));
});




/* ********************************************************** */


Route::get('raee/{id}', function( $id )
{
	$d = \App\CustomerInvoice::find( $id );

	$d->loadLineEcotaxes();

    return redirect()->back()
            ->with('success', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $id], 'layouts'));
});


Route::get('peon', function()
{
/*
	$sdds = \aBillander\SepaSpain\SepaDirectDebit::find(13);

	$sdds->updateTotal();
*/
	//
/*
	$d = \App\CustomerOrder::find(36);

	$d->loadLineEcotaxes();

	$d->ecotaxesonscreen();
*/
    $ecotax_id = 1;
    $date_from = '2019-09-26';  // .' 00:00:00'
    $date_to   = '2019-09-26';    // .' 23:59:59'
    
    // Lets see:
    $lines =  \App\CustomerOrderLine::
                  where('line_type', 'product')
                ->whereHas('ecotax', function ($query) use ($ecotax_id) {

                        if ( (int) $ecotax_id > 0 )
                            $query->where('id', $ecotax_id);
                })
                ->whereHas('document', function ($query) use ($date_from, $date_to) {

                        $query->where('document_date', '>=', $date_from.' 00:00:00');
                        $query->where('document_date', '<=', $date_to.' 23:59:59');
                })
                ->get();

    foreach ($lines as $line) {
    	# code...
    	abi_r($line->id);
    }
    abi_r( $lines->sum('ecotax_total_amount') );
});


/* ********************************************************** */



function checkRoute1($route='') {

	if ($route=='/') return true;

	$route=ltrim($route, '/');

    $routes = \Route::getRoutes()->getRoutes();
    foreach($routes as $r){
//        if($r->getUri() == $route){

abi_r($r->uri().' - '.$route);

        if($r->uri() == $route){
            return true;
        }
    }

    return false;
}

Route::get('peo', function()
{
	
	// $route=\Auth::user()->home_page;
	// checkRoute1($route);

	$pl = '{"id":5640,"parent_id":0,"number":"5640","order_key":"wc_order_5d76cef755c3b","created_via":"checkout","version":"3.1.1","status":"pending","currency":"EUR","date_created":"2019-09-09T22:15:19","date_created_gmt":"2019-09-09T22:15:19","date_modified":"2019-09-09T22:15:19","date_modified_gmt":"2019-09-09T22:15:19","discount_total":"0.00","discount_tax":"0.00","shipping_total":"0.00","shipping_tax":"0.00","cart_tax":"5.35","total":"58.83","total_tax":"5.35","prices_include_tax":false,"customer_id":163,"customer_ip_address":"213.194.146.126","customer_user_agent":"mozilla\/5.0 (windows nt 10.0; win64; x64; rv:68.0) gecko\/20100101 firefox\/68.0","customer_note":"","billing":{"first_name":"M Rosa","last_name":"Zamora Cobo","company":"","address_1":"C\/ Juan XXIII N. 8","address_2":"","city":"La Puebla del R\u00edo","state":"SE","postcode":"41130","country":"ES","email":"shorbydos@hotmail.com","phone":"637353533"},"shipping":{"first_name":"M Rosa","last_name":"Zamora Cobo","company":"","address_1":"C\/ Santa Mar\u00eda, 5 Edificio Multiusos","address_2":"","city":"La Puebla del R\u00edo","state":"SE","postcode":"41130","country":"ES"},"payment_method":"redsys","payment_method_title":"Tarjeta","transaction_id":"","date_paid":null,"date_paid_gmt":null,"date_completed":null,"date_completed_gmt":null,"cart_hash":"8e5a9f749f1cd1441c089a87cf8602a9","meta_data":[],"line_items":[{"id":5748,"name":"Pan de Arroz con masa madre ECO SG pack 4 uds","product_id":4506,"variation_id":0,"quantity":14,"tax_class":"10","subtotal":"53.48","subtotal_tax":"5.35","total":"53.48","total_tax":"5.35","taxes":[{"id":4,"total":"5.348","subtotal":"5.348"}],"meta_data":[],"sku":"4001","price":3.819999999999999840127884453977458178997039794921875}],"tax_lines":[{"id":5750,"rate_code":"ES-IMPUESTO-1","rate_id":4,"label":"Impuesto","compound":false,"tax_total":"5.35","shipping_tax_total":"0.00","meta_data":[]}],"shipping_lines":[{"id":5749,"method_title":"Env\u00edo gratuito","method_id":"advanced_free_shipping","total":"0.00","total_tax":"0.00","taxes":[],"meta_data":[]}],"fee_lines":[],"coupon_lines":[],"refunds":[]}';

	abi_r( json_decode($pl, true) );

});



// https://pineco.de/handling-webhooks-with-laravel/
// https://medium.com/team-culqi/usando-webhooks-con-laravel-1fa6d707bdba


Route::post('wooc/webhook', function()
{
	// we need to disable the CSRF token validation for this route. 
	// Open the VerifyCsrfToken middleware and add the route to the $except property.

	// $secret = 'Q0S,fT$~VBdg/[o`QXvQ?Zyd0B1%PX)5grt8g2B>1PFjs/BlSl';
	$secret = config('woocommerce.webhooks.product_updated');

	$wc_signature = "X-WC-Webhook-Signature";

	$hookSignature = request()->headers->get($wc_signature);

	$request_body = request()->getContent();
	$payLoad = json_decode(request()->getContent(), true);


	$HashSignature = base64_encode(hash_hmac('sha256',$request_body, $secret, true));

	// Step 1: Log (ActivityLogger)

	// Step 2: send email 
	$notify_to = 'lidiamartinez@laextranatural.es';

	$product_id = $payLoad['id'];

	\App\Configuration::updateValue('Z_WC_hookSignature', $hookSignature);


	\App\Configuration::updateValue('Z_WC_HashSignature', $HashSignature);


	\App\Configuration::updateValue('Z_WC_request_body', $request_body);


	\App\Configuration::updateValue('Z_WC_request_body_id', $product_id);


});




/* ********************************************************** */

Route::get('s27', function()
{

	$p = \App\Product::find(90);

	abi_r($p->getStockByWarehouse(1));
	abi_r($p->getStockByWarehouse(2));
	abi_r($p->getStock());
	// abi_r($p->);

	die();

	$p->setStockByWarehouse(2, 19);
	abi_r('$p -> setStockByWarehouse(2, 0)');
	abi_r($p->getStockByWarehouse(2));
	abi_r($p->getStock());
	// abi_r($p->);

	die();


	//
	$ws = \App\Warehouse::find(2);

	abi_r( $ws->products->count() );

	abi_r( $ws->products );

	foreach ($ws->products as $product) {
		# code...
		abi_r('['.$product->pivot->product_id.'] ' .$product->pivot->quantity);
	}

	abi_r( $ws->productlines->count() );

	foreach ($ws->productlines as $productline) {
		# code...
		abi_r('['.$productline->product->id.'] ' .$productline->quantity);
	}

});





/* ********************************************************** */

Route::get('v32', function()
{
	//
	$i=\App\CustomerInvoice::find(38);

	$i->totalsonscreen();
/*
	$v = \App\Payment::find(32);

	abi_r($v->customerinvoice->nextPayment());
*/

});


Route::get('v29', function()
{
	//
	$d = \App\CustomerInvoice::find(29);

	$d->checkPaymentStatus();

	abi_r($d->payment_status_name);


});




/* ********************************************************** */

Route::get('getkeys', function()
{
	//
	$keys = \App\Configuration::all();

	foreach ($keys as $k) {
		# code...
		echo '[\''.$k->name.'\', \''.$k->value.'\'],<br>';
	}


});

Route::get('/setkeys', function () {


	if (file_exists(__DIR__.'/etc/ckeys.php')) {
	    include __DIR__.'/etc/ckeys.php';
	} else 
		abi_r('Nothing to do here', true);

    foreach ($confs as $v){

        \App\Configuration::updateValue( $v[0] , $v[1] );
    	echo $v[0] .' - '. $v[1].'<br>';
    }

});

Route::get('/p', function () {

  $base_price = -100.0;
  $rule_percent = 21.0;
  $p = \App\Price::create([$base_price, $base_price*(1.0+$rule_percent/100.0)]);

  abi_r($p);
});

Route::get('/cccs', function () {

	if (file_exists(__DIR__.'/etc/ccc_data.php')) {
	    include __DIR__.'/etc/ccc_data.php';
	} else 
		abi_r('Nothing to do here', true);

	$i_ok = $i_ko = 0;

	foreach ($ccc_data as $row) {
		# code...
		$reference = trim($row[0]);

		$ccc = [
			'bank_name'   => trim($row[1]), 
			'ccc_entidad' => trim($row[2]),
			'ccc_oficina' => trim($row[3]),
			'ccc_control' => trim($row[4]),
			'ccc_cuenta'  => trim($row[5]),

			'iban' => trim($row[6]),
			'swift' => '',
		];

		$customer = \App\Customer::where('reference_external', $reference)->with('bankaccount')->first();

		if ( !$customer )
		{
			$i_ko++;

			abi_r("El Cliente de FactuSOL <strong>$reference</strong> NO existe en aBillander.");
			continue;
		}

        $bankaccount = $customer->bankaccount;

        if ( $bankaccount )
        {
            // Update
            $bankaccount->update($ccc);
        } else {
            // Create
            $bankaccount = \App\BankAccount::create($ccc);
            $customer->bankaccounts()->save($bankaccount);

            $customer->bank_account_id = $bankaccount->id;
            $customer->save();
        }

        $i_ok++;

		// abi_r($ccc);
	}

	abi_r("En total ".($i_ok + $i_ko)." procesados. Hay $i_ko errores");


	abi_r('Done!', true);
});



Route::get('/pdflayout/', function () {
  $pdf = PDF::loadView('.pdflayout.pdflayout');
  return $pdf->stream('pdflayout.pdf');
});

Route::get('/momo', function () {
  $a=10;
  $b=-$a;

  echo $b;
});


Route::get('test57', function()
{

/*    
	$p = \App\Product::find(89);

	$c = \App\Customer::find(1);

	$rules = $c->getPriceRules($p); abi_r($rules);

	$pr = $p->getPrice();  abi_r($pr);

	abi_r( $pr->applyPriceRules( $rules, 15 ));
*/
/*  
	$i = \App\CustomerInvoice::find(21);

	// abi_r($i->leftAscriptions);
	// abi_r(\App\CustomerShippingSlip::class);

	foreach ($i->leftShippingSlips() as $v) {
		# code...
		abi_r($v->id);
	}
*/

/*	$a= \App\CustomerShippingSlip::find(2);

	abi_r($a->customerinvoice());
*/

	abi_r( abi_date_form_short( 'now' ) );

	
});




/* ********************************************************** */

Route::get('test', function()
{
/*
	$a = \App\ProductBOM::create([
		'alias' => 'firstBOM', 
		'name' => 'Primera Lista de Materiales', 
		'quantity' => 5, 
		'measure_unit_id' => 1, 
	]);
*/	
	$a = \App\ProductBOM::find(1);

	$data = ['line_sort_order' => 20, 
             'product_id' => 1, 
             'quantity' => 12, 
             'measure_unit_id' => 1, 
             'scrap' => 10, 
             ];

	$b = \App\ProductBOMLine::create( $data );

	$a->BOMlines()->save($b);

//	abi_r($a->measureunit);
});


/* ********************************************************** */

Route::get('test1', function()
{
/*
	$a = \App\ProductBOM::create([
		'alias' => 'firstBOM', 
		'name' => 'Primera Lista de Materiales', 
		'quantity' => 5, 
		'measure_unit_id' => 1, 
	]);
*/	
	$a = \App\ProductBOM::find(1);

//	abi_r($a);

	foreach ($a->BOMlines as $bl) {

		abi_r($bl->product->name);
		abi_r($bl->quantity.' '.$bl->scrap);
		abi_r($bl->measureunit->sign);

	}

//	abi_r($a->measureunit);
});


/* ********************************************************** */

Route::get('test2', function()
{
/*
	$a = \App\ProductBOM::create([
		'alias' => 'firstBOM', 
		'name' => 'Primera Lista de Materiales', 
		'quantity' => 5, 
		'measure_unit_id' => 1, 
	]);
*/	
//	$a = \App\ProductionSheet::find(1);

//	abi_r($a->due_date);

//	$c = \App\Customer::find(1);

//	abi_r( $c->addresses );

/*
	abi_r(\App\Context::getContext()->company->currency);

	$t = \App\Tax::find(1);
	abi_r($t);
	abi_r((string) $t);
	abi_r($t->percent);
	abi_r('xx: '.$t->as_percent('percent'));
	abi_r($t->as_percentable($t->percent));
*/

	$order = \App\CustomerOrder::findOrFail( 1 );
//	abi_r($order->taxingaddress);


        $order_line = \App\CustomerOrderLine::findOrFail( 3 );

        $product = $order_line->product;
        $tax = $product->tax;

        // Let's deal with taxes
        $rules = $product->getTaxRules( $order->taxingaddress,  $order->customer );

        $base_price = $order_line->quantity*$order_line->unit_final_price;

        $order_line->total_tax_incl = $order_line->total_tax_excl = $base_price;

		foreach ( $rules as $rule ) {

			$line_tax = new \App\CustomerOrderLineTax();

				$line_tax->name = $tax->name . ' | ' . $rule->name;
				$line_tax->tax_rule_type = $rule->rule_type;

				$p = \App\Price::create([$base_price, $base_price*(1.0+$rule->percent/100.0)], $order->currency, $order->currency_conversion_rate);

				$p->applyRounding( );

				$line_tax->taxable_base = $base_price;
				$line_tax->percent = $rule->percent;
				$line_tax->amount = $rule->amount;
				$line_tax->total_line_tax = $p->getPriceWithTax() - $p->getPrice() + $p->as_priceable($rule->amount);

				$line_tax->position = $rule->position;

				$line_tax->tax_id = $tax->id;
				$line_tax->tax_rule_id = $rule->id;

				$line_tax->save();
				$order_line->total_tax_incl += $line_tax->total_line_tax;

				$order_line->CustomerOrderLineTaxes()->save($line_tax);
				$order_line->save();

		}

        abi_r($order_line->total_tax_excl);
        abi_r($order_line->total_tax_incl);
});


/* ********************************************************** */

Route::get('test3', function()
{
/*
	$tr = \App\TaxRule::find(3);

	abi_r($tr->name);
	abi_r($tr->tax->name);
	abi_r($tr->fullName);
	abi_r($tr->fullname.'o');
*/
/*
	$order = \App\CustomerOrder::findOrFail( 1 );

	$order->document_discount_percent = 0;

	$order->makeTotals();
*/
//	$order = \App\CustomerOrder::findOrFail( 15 );

//	abi_r('>>>> '.$order->getLastLineSortOrder());

/*
	$c = collect();
	abi_r( intval($c->max('line_sort_order')) );
*/
/*
	$l = new \Queridiam\FSxConnector\FsxLogger;
/ * * /
	$l->reset();

	$l->start();

	$l->write('Hola', 'INFO');

	$l->stop();
/ * * /

	$l->write('Hola', 'ERROR');
//	abi_r($l);

	$micro_date = microtime();
$date_array = explode(" ",$micro_date);
$date = date("Y-m-d H:i:s",$date_array[1]);
echo "$micro_date Date: $date:" . $date_array[0];
*/

/*
	$c = \App\Customer::find(1);

	$a = $c->address;

	$res = $a->getTaxes( );

	foreach ($res as $r) {
		abi_r( $r->taxrules()->sum('percent') );
	}

	abi_r($a->getTaxList());
	
	abi_r($a->getTaxPercentList());
	
	abi_r($a->getTaxREPercentList());
	
	abi_r($a->getTaxWithREPercentList());
*/
	$c = \App\Customer::find(4);

	$l = new \App\ActivityLogger();
	$l->empty();

	$l->log_name = 'testTOR';
	$l->description = 'Lorem Ipsum';

	$l->start();

	$l->timer_start();

	for ($i=0;$i<4;$i++){
			$l->log('INFO', 'Culo', [
			'alias' => 'firstBOM', 
			'name' => 'Primera Lista de Materiales', 
			'quantity' => 5, 
			'measure_unit_id' => 1, 
		]);
	}

	$l->stop();

	abi_r($l->timer_stop('display'));

//	$l->reset();

	// abi_r($c->hasLogs());
});


/* ********************************************************** */




/* ********************************************************** */

Route::get('-test4', function()
{
	//
	/*
	$c = \App\Customer::find(3);
	$p = \App\Product::find(21);

	$pri = $c->getPrice($p);

	foreach ($p->pricelists as $pl) {abi_r($pl->id.' - '.$pl->name);}

	*/
/*
	$table_name = 'languages';

	$columns = DB::select('show columns from ' . $table_name);
	foreach ($columns as $value) {
	   // echo "'" . $value->Field . "' => '" . $value->Type . "|" . ( $value->Null == "NO" ? 'required' : '' ) ."', <br/>" ;

		abi_r($value);
	}
*/

/*
	$s = \App\Supplier::findOrFail(1);

	abi_r($s->products()->count());

	$p = \App\Product::findOrFail(6);

	abi_r($p->supplier);

*/

/*
	$logger = \App\ActivityLogger::setup( 'Import Products :: ' . \Carbon\Carbon::now()->format('Y-m-d H:i:s') );

        $logger->start();
*/


//        abi_r(config('mail.from.address'));
//        abi_r(config('mail.from.name'));


//        $v='b84222934';
//        abi_r("$v ".\App\Customer::check_spanish_nif_cif_nie($v));


/*
            $collection = \App\Address::where('addressable_type', "App\\Customer")->get(['id']);

            // $collection = \App\Address::whereHas('customer')->get(['id']);

            abi_r('App\\Customer\\3');

            abi_r($collection);

            // Address::destroy($collection->toArray());
*/
// echo snake_case(str_plural('CustomerOrder'));

            $o = \App\CustomerOrder::find(27);

            abi_r($o->makeTotalsByTaxMethod());die();

            $txs = \App\Tax::with('taxrules')->get();

            foreach ($txs as $tx) {
            	$lines = $o->customerorderlinetaxes->where('tax_id', $tx->id);

            	if ($lines->count()) 
            		{
		                foreach ($tx->taxrules as $rule) {
		                	# code...

		                	$line_rules = $lines->where('tax_rule_id', $rule->id);

							if ($line_rules->count()) 
							{
				                $taxable_base   = $line_rules->sum('taxable_base'); 
				                $total_line_tax = $line_rules->sum('total_line_tax');

		            			abi_r($tx->name.' - '.$lines->count().' - '.$rule->name.' - '.$line_rules->count());
		            			abi_r($taxable_base.' - '.$total_line_tax);
							}
		                }

            			// abi_r($lines);
            		} 
            }

            // abi_r( $o->customerorderlinetaxes->groupBy(['tax_id', 'tax_rule_id'], $preserveKeys = true) );
});

Route::get('test4', function()
{

            // $o = \App\CustomerOrder::find(50)->customerordertaxes();
            // $o = \App\CustomerOrder::find(50)->xdocumentlines();

	  abi_r(\App\CustomerInvoice::find(29)->totals(), true);
/* * /
	$l = \App\CustomerOrder::find(50)->lines()->first();

	abi_r($l->id);

	$t = $l->linetaxes()->first();


	abi_r($t->documentline->id);
	abi_r($t->currency);
/ * */

/*
// http://otroblogmas.com/parsear-strings-formato-camelcase-php/
$str = 'CustomerOrderLineTax';
$str = snake_case($str);

$segments = array_reverse(explode('_', $str));

//echo studly_case($segments[0]);

	$order_line = \App\CustomerOrderLine::first();

	echo $order_line->getClassLastSegment();
*/
//	$o = \App\CustomerOrder::find(52);

//	$o->makeTotals();

/*
	$directories = \File::directories(resource_path().'/theme');
	$directories = array_diff($directories, ['.', '..']);

	abi_r(resource_path().'/theme');

	dd($directories);
*/
	$product = \App\Product::find(1);

//        $product->quantity_onhand = $product->getStock();
//        $product->save();
	foreach ($product->warehouses as $v) {
		# code...
		abi_r($v->pivot->quantity);
		$v->pivot->quantity = 17;
		$v->pivot->save();die();

	}

	abi_r($product->warehouses);die();
	abi_r($product->getStock());

});

Route::get('test41', function()
{
	$c=\App\Country::find(\App\Configuration::get('DEF_COUNTRY'));

//	abi_r($c->hasState(341));

//	abi_r(\App\Category::where('id', 122345)->exists());

	$d = 'aaaaaa-1';

	abi_r($d.' - '.(int)$c->checkIdentification($d));

});

// use Storage;

/* ********************************************************** */



/* ********************************************************** */



// Secure-Routes
Route::group(['middleware' =>  ['authAdmin']], function()
{
        
		Route::get('create', function()
		{
			$value = config('app.url');

			abi_r($value);

		});

});


Route::get('updates/xtra', function()
{

// 2018-07-09

	$logger = \App\ActivityLogger::setup( 'aBillander Project Updates', 'devMessenger-'.md5( config('app.url') ) );

	$logger->empty();

	$logger->log("SUCCESS", 'Terminado - Interface de Configuración en <i>Lara Billander -> Configuración</i>', []);

	$logger->log("SUCCESS", 'Terminado - Importación de Productos (Elaborados)', []);

	$logger->log("SUCCESS", 'Terminado - Importación de Tarifas', []);

	$logger->log("SUCCESS", 'Terminado - Importación de Clientes', []);

	$logger->log("SUCCESS", 'Terminado - Pedidos de Clientes', []);

	$logger->log("SUCCESS", 'Terminado - Importación de Pedidos desde WooCommerce', []);

	$logger->log("SUCCESS", 'Terminado - Exportar Pedidos y Clientes a FactuSOL', []);

	$logger->log("SUCCESS", 'Terminado - Añadir Pedidos a Hoja de Producción', []);

//	$logger->log("INFO", 'Próximos pasos:<br />- Terminar Exportar Pedidos y Clientes a FactuSOL.<br />- Añadir Pedidos a Hoja de Producción.', []);

	return redirect('/');

});


Route::get('deletes/{id}', function($id)
{
	$l = \App\ActivityLogger::findOrFail($id);

	$l->delete();

	return redirect()->route('home');

});


/* ********************************************************** */


Route::get('abccdash', function()
{
	return view('abcc.home');
});


/* ********************************************************** */


Route::get('curr', function()
{
	$l = App\CustomerOrderLine::find(141);

	abi_r($l->customerorder->id);
});


/* ********************************************************** */


Route::get('sku/{sku}', function( $sku )
{
	// $sku = $reques;
	$p = aBillander\WooConnect\WooProduct::fetch( $sku );

	abi_r($p, true);

	$images = $p['images'] ?? [];

	if ( $images && count($images) )
	{
		// Initialize with something to show
		$img_src  = $images[0]['src']  ?? '';
		$img_name = $images[0]['name'] ?? '';
		$img_alt  = $images[0]['alt']  ?? '';

		foreach ($images as $image)
		{
			if ($image['position'] == 0)
			{
				$img_src  = $image['src'];
				$img_name = $image['name'];
				$img_alt  = $image['alt'];
				break;
			}
		}

	} else {

		$img_src = 'https://www.laextranatural.com/wp-content/plugins/woocommerce/assets/images/placeholder.png';
		
	}

	abi_r('<img src="'.$img_src.'" id="imLogo" name="imLogo" alt="'.$img_alt.'" border="0">');

});



/* ********************************************************** */


Route::get('desc/{sku}', function( $sku )
{
	// $sku = $reques;
	$p = aBillander\WooConnect\WooProduct::fetch( $sku );

	$abi_p = \App\Product::where('reference', $sku)->first();

	// abi_r($p, true);

	if ( $p && $abi_p )
	{

		$abi_p->update([
					'description' => $p['description'],
					'description_short' => $p['short_description'],
			]);

	}

});


/* ********************************************************** */


Route::get('fpago', function()
{
    // aBillander Methods
    $pgatesList = \App\PaymentMethod::select('id', 'name')->orderby('name', 'desc')->get()->toArray();

    $l= [];

    foreach($pgatesList as $k => $v)
    {
    	$l[] = 
		        [
		            'id' => '00'.$v['id'],
		            'name' => $v['name']
		        ];
    }

    $ll =collect($l)->pluck('name', 'id')->toArray();

    \App\Configuration::updateValue('FSX_FORMAS_DE_PAGO_CACHE', json_encode($ll));



    abi_r(  \App\Configuration::get('FSX_FORMAS_DE_PAGO_CACHE') );

    $fsolpaymethods = \Queridiam\FSxConnector\FSxTools::getFormasDePagoList();
    abi_r( ($fsolpaymethods ) );

});


/* ********************************************************** */


Route::get('ordersupdate', function()
{
    // 
    $os = \App\CustomerOrder::all();

    foreach ($os as $o) {

    	//	abi_r($o->id);

    	if( ($o->total_currency_tax_excl == 0) && ($o->total_currency_tax_incl == 0) ){
    		$o->total_currency_tax_excl = $o->total_tax_excl;
    		$o->total_currency_tax_incl = $o->total_tax_incl;

    		abi_r($o->id);

    		$o->save();
    	}
    }

    echo 'Done!';

});


/* ********************************************************** */

use Queridiam\FSxConnector\Seccion;
use Queridiam\FSxConnector\Familia;
use Queridiam\FSxConnector\Articulo;
use Queridiam\FSxConnector\Stock;
use Queridiam\FSxConnector\Tarifa;
use Queridiam\FSxConnector\FormaPago;


Route::get('fsec', function()
{
//	\DB::enableQueryLog();
	$l = Seccion::with('familias')->find('1');
	$l = Familia::with('articulos')->find('4');
	\DB::enableQueryLog();
	$l = Articulo::find('BURGUER002');
//	$l1 = Stock::with('articulo')->find('BURGUER002');

//	abi_r($l->stock_actual());
/*
	abi_r($l->stock);

	abi_r(\DB::getQueryLog());

	// abi_r(Tarifa::tarifa_codigo().' - '.Tarifa::tarifa_nombre());

	$t = Tarifa::find(\App\Configuration::get('FSOL_TCACFG'));

	// abi_r($t->precio('BURGUER002'));
	abi_r(Articulo::doesntHave('product')->with('familia.category')->with('stock')->get());

	\App\Configuration::updateValue('FSX_CATALOGUE_LAST_RUN_DATE', \Carbon\Carbon::now()->format('Y-m-d H:i:s'));
*/

	$today = \Carbon\Carbon::now();
	
	abi_r($today);
	abi_r($today->tz('UTC'));

});


/* ********************************************************** */


Route::get('fixcustomers', function()
{
    // 
    $cs = \App\Customer::where('webshop_id', '!=', null)->where('webshop_id', '!=', '')->get();

    foreach ($cs as $c) {

    	//	
    	$id = $c->id;
    	$wid = $c->webshop_id;
    	$ref = $c->reference_external;

    	$fid = \aBillander\WooConnect\FSxTools::translate_customers_fsol($wid);

    	$flag = '';
    	if ($ref != $fid) {

    		$c->reference_external = $fid;
    		$c->save();

    		$flag = '*';
    	}


    	abi_r("id $id - wid $wid - ref $ref - fid $fid $flag");



    		// $c->save();
    }

    echo 'Done!';

});



/* ********************************************************** */


Route::get('wship', function()
{

	$a = \aBillander\WooConnect\WooOrder::getShippingMethodId('flat_rate');

	abi_r($a);

});


/* ********************************************************** */


Route::get('user', function()
{

	abi_r(Auth::user('customer'));

});


/* ********************************************************** */


Route::get('test51', function()
{

        $customeruser = \App\CustomerUser::find(13);     // ->load('customer');
        $todo = \App\Todo::find(4);

        // event(new \App\Events\CustomerRegistered($customeruser));

        $l = new \App\Listeners\NewCustomerRegistered();

        $l->customeruser = $customeruser;
        $l->todo = $todo;

        // Send Confirmation email to Customer
        $l->handleCustomerNotification();

        // Send mail to admin
//        $l->handleAdminNotification();

        $a = new MeasureUnitsTableSeeder();


});

/*
use Illuminate\Database\Seeder;

use App\MeasureUnit;
use App\Configuration;
  
class MeasureUnitsTableSeeder extends Seeder {
  
    public function run() {
        MeasureUnit::truncate();
  
        $munit = MeasureUnit::create( [
            'type' => 'Quantity', 
            'sign' => 'ud.', 
            'name' => 'Unidad(es)', 
            'decimalPlaces' => 0, 
            'conversion_rate' => 1.0,
            'active' => 1,
        ] );

        Configuration::updateValue('DEF_MEASURE_UNIT_FOR_PRODUCTS', $munit->id);
        Configuration::updateValue('DEF_MEASURE_UNIT_FOR_BOMS'    , $munit->id);
    }
}
*/

Route::get('test52', function()
{

//        abi_r(\App\CustomerInvoice::getPaymentStatusList());

/*
	$l = new \App\CustomerInvoiceLine;

	abi_r($l->getClassName());
	abi_r($l->getClassLastSegment());

	abi_r($l->getParentClassName());
	abi_r($l->getParentClassSnakeCase());
*/

$alltaxes = \App\Tax::get()->sortByDesc('percent');
$alltax_rules = \App\TaxRule::get();

	$document = \App\CustomerInvoice::find(2);

	$totals = $document->totals();

	foreach ($alltaxes as $alltax) {
		# code...
		if ( !( $total = $totals->where('tax_id', $alltax->id)->first() ) ) continue;
	}





	abi_r($document->totals());
});


/* ********************************************************** */


Route::get('test5', function()
{
	$customerId = 13;
	$productId = 21;


	// https://stackoverflow.com/questions/41679771/eager-load-relationships-in-laravel-with-conditions-on-the-relation?rq=1

	$lines = \App\CustomerOrderLine::whereHas(

	        'customerorder', function ($order) use ($customerId) {
	            return $order->where('customer_id', $customerId);
	        }


	)->whereHas(

	        'product', function ($product) use ($productId) {
	            return $product->where('id', $productId);
	        }

	)->with([
//https://stackoverflow.com/questions/41679771/eager-load-relationships-in-laravel-with-conditions-on-the-relation?rq=1
// https://stackoverflow.com/questions/25700529/laravel-eloquent-how-to-order-results-of-related-models
// https://laracasts.com/discuss/channels/eloquent/order-by-on-relationship
// https://laracasts.com/discuss/channels/eloquent/eloquent-order-by-related-table
// https://stackoverflow.com/questions/40837690/laravel-eloquent-sort-by-relationship
/*
	        Won't work. Why?
	        'customerorder' => function ($order) {
	            return $order->orderBy('document_date', 'desc');
	        }
*/
			'customerorder',

	        'product'

	])->join('customer_orders', 'customer_order_lines.customer_order_id', '=', 'customer_orders.id')
	  ->orderBy('customer_orders.document_date', 'desc')
          ->orderBy('customer_orders.id', 'desc')
          ->select('customer_order_lines.*')->get();

	// abi_r($lines, true);


	foreach ($lines as $line) {
		# code...
		abi_r($line->customerorder->customer_id.' - '.$line->customerorder->document_date.' - '.$line->product->id.' - '.$line->id);
	}

	$customer = \App\Customer::find(13);
	$product = \App\Product::find(2122222);

	abi_r($customer->getLastPrice($product));


});


/* ********************************************************** */


Route::get('fts', function()
{
	// Illuminate\Support\Facades\DB::statement('ALTER TABLE products ADD FULLTEXT fulltext_index (name, reference, ean13, description)');

	$s = 'sam 49';
	$words = explode(' ', $s);

     abi_r(count($words));

	$ps = \App\Product::select('*')->searchWhithRelevance($s)->get();

	foreach ($ps as $p) {
	 	# code...
	 	abi_r($p->relevance_score.' - '.$p->reference.' - '.$p->ean13.' - '.$p->name);
	 } 

	 abi_r($ps);

});


/* ********************************************************** */


Route::get('seq', function()
{
	

	 abi_r(\App\CustomerShippingSlip::sequences());

	 $d = new \App\CustomerShippingSlip;

	 abi_r($d->sequenceList());

});


/* ********************************************************** */


Route::get('migratethis', function()
{


	// 2020-01-28
		Illuminate\Support\Facades\DB::statement("drop table if exists `suppliers`;");

		Illuminate\Support\Facades\DB::statement("create table `suppliers` (`id` int unsigned not null auto_increment primary key, `alias` varchar(32) null, `name_fiscal` varchar(128) not null, `name_commercial` varchar(64) null, `website` varchar(128) null, `customer_center_url` varchar(128) null, `customer_center_user` varchar(128) null, `customer_center_password` varchar(16) null, `identification` varchar(64) null, `reference_external` varchar(32) null, `accounting_id` varchar(32) null, `discount_percent` decimal(20, 6) not null default '0', `discount_ppd_percent` decimal(20, 6) not null default '0', `payment_days` varchar(16) null, `notes` text null, `customer_logo` varchar(128) null, `sales_equalization` tinyint not null default '0', `approved` tinyint not null default '1', `blocked` tinyint not null default '0', `active` tinyint not null default '1', `customer_id` int unsigned null, `currency_id` int unsigned not null, `language_id` int unsigned not null, `payment_method_id` int unsigned null, `bank_account_id` int unsigned null, `invoice_sequence_id` int unsigned null, `invoicing_address_id` int unsigned not null, `secure_key` varchar(32) not null, `import_key` varchar(16) null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;");


	die('OK');



	// 2010-01-09
	$ls = \App\EmailLog::where('userable_type', 'System')->get();

	foreach ($ls as $emaillog) {
		# code...
            $emaillog->userable_type = 'App\User';
            $emaillog->save();
	}


	die('OK');


	// 2020-01-01
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `addresses` ADD `shipping_method_id` INT(10) UNSIGNED NULL AFTER `country_id`;");


	// die('OK');


	// 2019-12-27
	
	Illuminate\Support\Facades\DB::statement("ALTER TABLE `shipping_methods` ADD `class_name` varchar(64) DEFAULT NULL AFTER `webshop_id`;");


	die('OK');


	// 2019-12-17
		
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` ADD `mrp_type` varchar(32) NOT NULL DEFAULT 'onorder' AFTER `procurement_type`;");

	// 2019-12-14
		Illuminate\Support\Facades\DB::statement("create table `delivery_route_lines` (`id` int unsigned not null auto_increment primary key, `line_sort_order` int null, `delivery_route_id` int unsigned not null, `customer_id` int unsigned null, `address_id` int unsigned null, `notes` text null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;");


		Illuminate\Support\Facades\DB::statement("create table `delivery_sheet_lines` (`id` int unsigned not null auto_increment primary key, `line_sort_order` int null, `delivery_sheet_id` int unsigned not null, `customer_id` int unsigned null, `address_id` int unsigned null, `route_notes` text null, `notes` text null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;");


		Illuminate\Support\Facades\DB::statement("create table `delivery_routes` (`id` int unsigned not null auto_increment primary key, `alias` varchar(32) not null, `name` varchar(64) not null, `driver_name` varchar(128) null, `active` tinyint not null default '1', `notes` text null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;");


		Illuminate\Support\Facades\DB::statement("create table `delivery_sheets` (`id` int unsigned not null auto_increment primary key, `sequence_id` int unsigned null, `document_prefix` varchar(8) null, `document_id` int unsigned not null default '0', `document_reference` varchar(64) null, `name` varchar(64) not null, `driver_name` varchar(128) null, `due_date` date not null, `active` tinyint not null default '1', `route_notes` text null, `driver_notes` text null, `notes` text null, `delivery_route_id` int unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;");
		
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `delivery_route_lines` ADD `active` INT(10) NOT NULL DEFAULT '1' AFTER `address_id`;");


	die('OK');


	// 2019-12-12
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `stock_movements` ADD `cost_price_after_movement`  DECIMAL(20,6) NULL AFTER `quantity_after_movement`;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `stock_movements` ADD `cost_price_before_movement` DECIMAL(20,6) NULL AFTER `quantity_after_movement`;");


	die('OK');

	// 2019-12-11
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `customers` ADD `invoice_sequence_id` INT(10) UNSIGNED NULL AFTER `bank_account_id`;");


	die('OK');



	// 2019-12-07
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `measure_units` CHANGE `conversion_rate` `type_conversion_rate` DECIMAL(20,6) NOT NULL DEFAULT '1.000000';");


	die('OK');



	// 2019-12-03
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `price_rules` ADD `conversion_rate` DECIMAL(20,6) NULL DEFAULT '1.0' AFTER `date_to`;");
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `price_rules` ADD `measure_unit_id` INT(10) UNSIGNED NULL AFTER `date_to`;");


	die('OK');


	// 2019-12-02
	$tables = ['customer_invoice', 'customer_shipping_slip', 'customer_quotation', 'customer_order'];

	foreach ($tables as $table) {
		# code...
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `".$table."_lines` ADD `pmu_conversion_rate` DECIMAL(20,6) NULL DEFAULT '1.0' AFTER `measure_unit_id`;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `".$table."_lines` ADD `package_measure_unit_id` INT(10) UNSIGNED NULL AFTER `measure_unit_id`;");

	}
	
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `product_measure_units` CHANGE `base_measure_unit_id` `stock_measure_unit_id` INT(10) UNSIGNED NOT NULL;");
		
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `cart_lines` ADD `pmu_conversion_rate` DECIMAL(20,6) NULL DEFAULT '1.0' AFTER `measure_unit_id`;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `cart_lines` ADD `package_measure_unit_id` INT(10) UNSIGNED NULL AFTER `measure_unit_id`;");

	die('OK');




	// 2019-11-22
	$tables = ['customer_invoice', 'customer_shipping_slip', 'customer_quotation', 'customer_order'];

	foreach ($tables as $table) {
		# code...
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `".$table."_lines` ADD `extra_quantity_label` varchar(128) null AFTER `quantity`;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `".$table."_lines` ADD `extra_quantity` DECIMAL(20,6) NULL DEFAULT '0.0' AFTER `quantity`;");

	}

	die('OK');


	// 2019-11-21
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `sepa_direct_debits` ADD `discount_dd` INT(10) NOT NULL DEFAULT '0' AFTER `group_vouchers`;");
	 

	die('OK');


	// 2019-11-18
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `carts` ADD `sub_tax_excl` DECIMAL(20,6) DEFAULT '0.0' AFTER `total_shipping_tax_excl`;");
	 
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `carts` ADD `sub_tax_incl` DECIMAL(20,6) DEFAULT '0.0' AFTER `total_shipping_tax_excl`;");
	 

	die('OK');



	// 2019-11-15
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_users` ADD `use_default_min_order_value` INT(10) NOT NULL DEFAULT '1' AFTER `enable_min_order`;");
	 

	die('OK');


	// 2019-11-13
		Illuminate\Support\Facades\DB::statement("DROP TABLE `carts`;");

		Illuminate\Support\Facades\DB::statement("create table `carts` (`id` int unsigned not null auto_increment primary key, `customer_user_id` int unsigned not null, `customer_id` int unsigned not null, `notes_from_customer` text null, `date_prices_updated` timestamp null, `total_items` int unsigned not null default '0', `total_products_tax_incl` decimal(20, 6) not null default '0', `total_products_tax_excl` decimal(20, 6) not null default '0', `total_shipping_tax_incl` decimal(20, 6) not null default '0', `total_shipping_tax_excl` decimal(20, 6) not null default '0', `document_discount_percent` decimal(20, 6) not null default '0', `document_discount_amount_tax_incl` decimal(20, 6) not null default '0', `document_discount_amount_tax_excl` decimal(20, 6) not null default '0', `document_ppd_percent` decimal(20, 6) not null default '0', `document_ppd_amount_tax_incl` decimal(20, 6) not null default '0', `document_ppd_amount_tax_excl` decimal(20, 6) not null default '0', `total_currency_tax_incl` decimal(20, 6) not null default '0', `total_currency_tax_excl` decimal(20, 6) not null default '0', `total_tax_incl` decimal(20, 6) not null default '0', `total_tax_excl` decimal(20, 6) not null default '0', `invoicing_address_id` int unsigned null, `shipping_address_id` int unsigned null, `shipping_method_id` int unsigned null, `carrier_id` int unsigned null, `currency_id` int unsigned not null, `payment_method_id` int unsigned null, `secure_key` varchar(32) not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;");


	// 2019-11-11
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `price_rules` ADD `name` varchar(128) null AFTER `id`;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `cart_lines` ADD `extra_quantity_label` varchar(128) null AFTER `extra_quantity`;");

	// 2019-11-10
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `price_rules` ADD `extra_quantity` DECIMAL(20,6) NOT NULL DEFAULT '0.0' AFTER `from_quantity`;");

		Illuminate\Support\Facades\DB::statement("DROP TABLE `cart_lines`;");

		Illuminate\Support\Facades\DB::statement("create table `cart_lines` (`id` int unsigned not null auto_increment primary key, `line_sort_order` int null, `line_type` varchar(32) not null default 'product', `product_id` int unsigned null, `combination_id` int unsigned null, `reference` varchar(32) null, `name` varchar(128) not null, `quantity` decimal(20, 6) not null, `measure_unit_id` int unsigned not null, `unit_customer_price` decimal(20, 6) not null default '0', `unit_customer_final_price` decimal(20, 6) not null default '0', `sales_equalization` tinyint not null default '0', `total_tax_incl` decimal(20, 6) not null default '0', `total_tax_excl` decimal(20, 6) not null default '0', `tax_percent` decimal(8, 3) not null default '0', `tax_se_percent` decimal(8, 3) not null default '0', `cart_id` int unsigned not null, `tax_id` int unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `cart_lines` ADD `extra_quantity` DECIMAL(20,6) NOT NULL DEFAULT '0.0' AFTER `quantity`;");


	// 2019-10-24
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `price_lists` ADD `last_imported_at` timestamp null DEFAULT NULL AFTER `currency_id`;");


	// 2019-10-23
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `payment_methods` CHANGE `payment_document_id` `payment_type_id` INT(10) UNSIGNED NULL DEFAULT NULL;");
	 

	// die('OK');

	// 2019-10-22
		Illuminate\Support\Facades\DB::statement("create table `payment_types` (`id` int unsigned not null auto_increment primary key, `alias` varchar(32) not null, `name` varchar(64) not null, `active` tinyint not null default '1', `accounting_code` varchar(32) null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;");
	 

	die('OK');


	// 2019-10-14
	$tables = ['customer_invoice', 'customer_shipping_slip', 'customer_quotation', 'customer_order'];

	foreach ($tables as $table) {
		# code...

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `".$table."_lines` ADD `ecotax_total_amount` DECIMAL(20,6) NOT NULL DEFAULT '0.0' AFTER `ecotax_amount`;");

	}

	die('OK');

	// 2019-10-10
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `sales_rep_users` ADD `allow_abcc_access` INT(10) NOT NULL DEFAULT '0' AFTER `active`;");
	 

	die('OK');
	 

	// 2019-10-01
	Illuminate\Support\Facades\DB::statement("create table `commission_settlements` (`id` int unsigned not null auto_increment primary key, `name` varchar(128) null, `document_date` datetime not null, `date_from` datetime not null, `date_to` datetime not null, `paid_documents_only` tinyint not null default '0', `status` varchar(32) not null default 'pending', `onhold` tinyint not null default '0', `total_commissionable` decimal(20, 6) not null default '0', `total_settlement` decimal(20, 6) not null default '0', `notes` text null, `close_date` datetime null, `posted_at` date null, `sales_rep_id` int unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci
;");

	Illuminate\Support\Facades\DB::statement("create table `commission_settlement_lines` (`id` int unsigned not null auto_increment primary key, `commissionable_id` int not null, `commissionable_type` varchar(191) not null, `document_reference` varchar(64) null, `document_date` datetime not null, `document_commissionable_amount` decimal(20, 6) not null default '0', `commission_percent` decimal(8, 3) not null default '0', `commission` decimal(20, 6) not null default '0', `commission_settlement_id` int unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;");


	 

	// 2019-09-22
	Illuminate\Support\Facades\DB::statement("ALTER TABLE `sepa_direct_debits` ADD `group_vouchers` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `onhold`;");


	// 2019-09-19
	\App\Configuration::updateValue('ABCC_ORDERS_NEED_VALIDATION', \App\Configuration::get('CUSTOMER_ORDERS_NEED_VALIDATION'));
	 

	die('OK');


	 \App\Configuration::updateValue('CUSTOMER_INVOICE_TAX_LABEL', 'IGIC');
	 \App\Configuration::updateValue('CUSTOMER_INVOICE_TAX_LABEL', 'IVA');

/*
	 INSERT INTO `configurations` (`id`, `name`, `description`, `value`, `created_at`, `updated_at`) VALUES (NULL, 'CUSTOMER_INVOICE_TAX_LABEL', NULL, 'IGIC', '2019-09-14 00:00:00', '2019-09-14 00:00:00');
*/
	 

	die('OK');
	

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `production_orders` ADD `required_quantity` DECIMAL(20,6) NOT NULL AFTER `procurement_type`;");


	die('OK');
	

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `production_orders` ADD `manufacturing_batch_size` INT(10) UNSIGNED NOT NULL DEFAULT '1' AFTER `work_center_id`;");


	// die('OK');

	

	 \App\Configuration::updateValue('ALLOW_IP_ADDRESSES', '');
	 \App\Configuration::updateValue('MAX_DB_BACKUPS', '30');
	 \App\Configuration::updateValue('MAX_DB_BACKUPS_ACTION', 'delete');



	die('OK');

	

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` CHANGE `manufacturing_batch_size` `manufacturing_batch_size` INT(10) UNSIGNED NOT NULL DEFAULT '1';");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` CHANGE `units_per_tray` `units_per_tray` INT(10) UNSIGNED NULL DEFAULT NULL;");


	die('OK');

	
/*
	\App\Configuration::updateValue('WOOC_STORE_URL',       'https://www.laextranatural.com/');
	\App\Configuration::updateValue('WOOC_CONSUMER_KEY',    'ck_1aebe81e2878ca6a92719fff45fd8266384b8898');
	\App\Configuration::updateValue('WOOC_CONSUMER_SECRET', 'cs_13eb029f00828058fa78670cd14a526fc8a7de9a');
*/		


	 \App\Configuration::updateValue('CUSTOMER_INVOICE_BANNER', 'Haga su pedido en www.gmdistribuciones.es');

	 // INSERT INTO `configurations` (`id`, `name`, `description`, `value`, `created_at`, `updated_at`) VALUES (NULL, 'CUSTOMER_INVOICE_BANNER', NULL, 'Haga su pedido en www.gmdistribuciones.es', '2019-08-05 00:00:00', '2019-08-05 00:00:00');


	die('OK');


	 \App\Configuration::updateValue('CUSTOMER_INVOICE_CAPTION', 'Según el Real Decreto 110/2015 tanto las lámparas led como bajo consumo están sometidas al RAE. Número de registro 6299.
Sus datos se encuentran registrados en una base propiedad de GUSTAVO MEDINA RODRIGUEZ DISTRIBUCIONES S.L.U., inscrita en la Agencia Española de Protección
de datos. Usted en cualquier momento puede ejercer sus derechos de acceso, rectificación, cancelación y/u oponerse a su tratamiento. Estos derechos
pueden ser ejercitados escribiendo a GUSTAVO MEDINA RODRIGUEZ, C/ PRIMAVERA, Nº 20 – 35018 LAS PALMAS DE GRAN CANARIA (LAS PALMAS).');

	 // UPDATE `configurations` SET `value` = 'Según el Real Decreto 110/2015 tanto las lámparas led como bajo consumo están sometidas al RAE. Número de registro 6299.\r\nSus datos se encuentran registrados en una base propiedad de GUSTAVO MEDINA RODRIGUEZ DISTRIBUCIONES S.L.U., inscrita en la Agencia Española de Protección\r\nde datos. Usted en cualquier momento puede ejercer sus derechos de acceso, rectificación, cancelación y/u oponerse a su tratamiento. Estos derechos\r\npueden ser ejercitados escribiendo a GUSTAVO MEDINA RODRIGUEZ, C/ PRIMAVERA, Nº 20 – 35018 LAS PALMAS DE GRAN CANARIA (LAS PALMAS).' WHERE `configurations`.`name` = 'CUSTOMER_INVOICE_CAPTION';


	die('OK');

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `carriers` ADD `alias` VARCHAR(32) NULL AFTER `id`;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `shipping_methods` ADD `alias` VARCHAR(16) NULL AFTER `user_id`;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `bank_accounts` ADD `creditorid` VARCHAR(30) NULL AFTER `swift`;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `bank_accounts` ADD `suffix` VARCHAR(3) NOT NULL DEFAULT '000' AFTER `swift`;");

	\App\Context::getContext()->company->bankaccount->update(['suffix' => '100']);


	die('OK');


	 \App\Configuration::updateValue('RECENT_SALES_CLASS', 'CustomerOrder');

	 // INSERT INTO `configurations` (`id`, `name`, `description`, `value`, `created_at`, `updated_at`) VALUES (NULL, 'RECENT_SALES_CLASS', NULL, 'CustomerOrder', '2019-08-05 00:00:00', '2019-08-05 00:00:00');


	die('OK');


		Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` CHANGE `machine_capacity` `machine_capacity` VARCHAR(16) NULL DEFAULT NULL;");


		Illuminate\Support\Facades\DB::statement("ALTER TABLE `production_orders` ADD `units_per_tray` DECIMAL(20,6) NULL AFTER `work_center_id`;");
	

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `production_orders` ADD `machine_capacity` VARCHAR(16) NULL AFTER `work_center_id`;");

		

	die('OK');




		Illuminate\Support\Facades\DB::statement("ALTER TABLE `production_order_lines` CHANGE `base_quantity` `bom_line_quantity` DECIMAL(20,6) NOT NULL;");
		

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `production_order_lines` ADD `bom_quantity` DECIMAL(20,6) NOT NULL AFTER `bom_line_quantity`;");
		

	die('OK');



		Illuminate\Support\Facades\DB::statement("ALTER TABLE `stock_count_lines` CHANGE `product_id` `product_id` INT(10) UNSIGNED NULL;");
		

	die('OK');



		Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_users` CHANGE `display_prices_tax_inc` `display_prices_tax_inc` INT(10) NOT NULL DEFAULT '0';");
		

	die('OK');



		Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_users` ADD `display_prices_tax_inc` INT(10) NOT NULL DEFAULT '0' AFTER `min_order_value`;");
		

	die('OK');


	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_shipping_slips` ADD `shipment_status` VARCHAR(32) NOT NULL DEFAULT 'pending' AFTER `import_key`;");
		

	die('OK');



		Illuminate\Support\Facades\DB::statement("create table `email_logs` (`id` int unsigned not null auto_increment primary key, `to` varchar(191) null, `subject` varchar(191) not null, `body` longtext not null, `from` varchar(191) null, `cc` varchar(191) null, `bcc` varchar(191) null, `headers` text null, `attachments` longtext null, `userable_id` int null, `userable_type` varchar(191) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci");
		

	die('OK');



		Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_users` ADD `is_principal` INT(10) UNSIGNED NOT NULL DEFAULT '1' AFTER `active`;");


		Illuminate\Support\Facades\DB::statement("UPDATE `customer_users` SET `is_principal` = '1' WHERE `customer_users`.`id` > 0;");


		Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_users` ADD `address_id` INT(10) UNSIGNED NULL AFTER `customer_id`;");


		Illuminate\Support\Facades\DB::statement("ALTER TABLE `sepa_direct_debits` ADD `onhold` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `status`;");
		

	die('OK');


		Illuminate\Support\Facades\DB::statement("CREATE TABLE `payment_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `payment_documents`
  ADD PRIMARY KEY (`id`);");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `payment_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");

	// ALTER TABLE `payments` CHANGE `down_payment` `is_down_payment` INT(10) UNSIGNED NOT NULL DEFAULT '0';

		Illuminate\Support\Facades\DB::statement("INSERT INTO `payment_documents` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'No asignado', '2019-06-20 08:59:05', '2019-06-20 08:59:05', NULL);");



		Illuminate\Support\Facades\DB::statement("CREATE TABLE `production_order_tool_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `tool_id` int(10) UNSIGNED NOT NULL,
  `reference` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(20,6) NOT NULL,
  `location` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `production_order_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `production_order_tool_lines`
  ADD PRIMARY KEY (`id`);");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `production_order_tool_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;");

	// ALTER TABLE `payments` CHANGE `down_payment` `is_down_payment` INT(10) UNSIGNED NOT NULL DEFAULT '0';

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `payment_methods` ADD `payment_document_id` INT(10) UNSIGNED NULL AFTER `active`;");


	Illuminate\Support\Facades\DB::statement("create table `payment_documents` (`id` int unsigned not null auto_increment primary key, `name` varchar(128) not null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;");


	Illuminate\Support\Facades\DB::statement("INSERT INTO `payment_documents` (`name`, `created_at`, `updated_at`, `deleted_at`) VALUES
('No asignado', '2019-06-20 10:59:05', '2019-06-20 10:59:05', NULL);");


		Illuminate\Support\Facades\DB::statement("ALTER TABLE `payments` ADD `is_down_payment` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `name`;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `payments` ADD `auto_direct_debit` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `name`;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `payments` ADD `payment_method_id` INT(10) UNSIGNED NULL AFTER `paymentorable_type`;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `payments` ADD `payment_document_id` INT(10) UNSIGNED NULL AFTER `paymentorable_type`;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `payments` ADD `bank_order_id` INT(10) UNSIGNED NULL AFTER `paymentorable_type`;");


	die('OK');


	Illuminate\Support\Facades\DB::statement("DROP TABLE `sepa_direct_debits`;");


		Illuminate\Support\Facades\DB::statement("create table `sepa_direct_debits` (`id` int unsigned not null auto_increment primary key, `sequence_id` int unsigned null, `document_prefix` varchar(8) null, `document_id` int unsigned not null default '0', `document_reference` varchar(64) null, `iban` varchar(34) not null, `swift` varchar(11) null, `creditorid` varchar(30) null, `currency_iso_code` varchar(3) not null, `currency_conversion_rate` decimal(20, 6) not null default '1', `scheme` varchar(32) not null default 'CORE', `status` varchar(32) not null default 'pending', `notes` text null, `document_date` datetime not null, `validation_date` datetime null, `payment_date` datetime null, `posted_at` date null, `total` decimal(20, 6) not null default '0', `bank_account_id` int unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;");
	


	die('OK');


		Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` ADD `units_per_tray` DECIMAL(20,6) NULL AFTER `work_center_id`;");
	

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` ADD `machine_capacity` DECIMAL(20,6) NULL AFTER `work_center_id`;");

	die('OK');


	$tables = ['customer_invoice', 'customer_shipping_slip', 'customer_quotation', 'customer_order'];

	foreach ($tables as $table) {
		# code...

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `".$table."_lines` ADD `ecotax_id` INT(10) UNSIGNED NULL AFTER `tax_id`;");
	

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `".$table."_lines` ADD `ecotax_amount` DECIMAL(20,6) NOT NULL DEFAULT '0.0' AFTER `tax_percent`;");

	}

	die('OK');


		// 
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `companies` ADD `bank_account_id` int(10) UNSIGNED DEFAULT NULL AFTER `language_id`;");


	die('OK');

		Illuminate\Support\Facades\DB::statement("create table `sepa_direct_debits` (`id` int unsigned not null auto_increment primary key, `sequence_id` int unsigned null, `document_prefix` varchar(8) null, `document_id` int unsigned not null default '0', `document_reference` varchar(64) null, `iban` varchar(34) not null, `swift` varchar(11) null, `creditorid` varchar(30) null, `currency_iso_code` varchar(3) not null, `currency_conversion_rate` decimal(20, 6) not null default '1', `local_instrument` varchar(32) not null default 'CORE', `status` varchar(32) not null default 'pending', `document_date` datetime not null, `validation_date` datetime null, `payment_date` datetime null, `posted_at` date null, `total` decimal(20, 6) not null default '0', `bank_account_id` int unsigned not null) default character set utf8mb4 collate utf8mb4_unicode_ci;");








	die('OK');
	

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `bank_accounts` CHANGE `swift` `swift` VARCHAR(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");

//	die('OK');
	

	Illuminate\Support\Facades\DB::statement("DROP TABLE `bank_accounts`;");

		Illuminate\Support\Facades\DB::statement("create table `bank_accounts` (`id` int unsigned not null auto_increment primary key, `name` varchar(64) not null, `bank_name` varchar(64) not null, `ccc_entidad` varchar(4) not null, `ccc_oficina` varchar(4) not null, `ccc_control` varchar(2) not null, `ccc_cuenta` varchar(10) not null, `iban` varchar(34) not null, `swift` varchar(11) not null, `mandate_reference` varchar(35) null, `mandate_date` date null, `notes` text null, `bank_accountable_id` int not null, `bank_accountable_type` varchar(191) not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;");

//	die('OK');

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `customers` ADD `bank_account_id` int(10) UNSIGNED DEFAULT NULL AFTER `payment_method_id`;");
	
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `customers` ADD `discount_ppd_percent` DECIMAL(20,6) NOT NULL DEFAULT '0.0' AFTER `accounting_id`;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `customers` ADD `discount_percent` DECIMAL(20,6) NOT NULL DEFAULT '0.0' AFTER `accounting_id`;");

//	die('OK');

	// Descuento Pronto Pago
	$tables = ['customer_invoices', 'customer_shipping_slips', 'customer_orders', 'customer_quotations'];

	foreach ($tables as $table) {
		# code...
		Illuminate\Support\Facades\DB::statement("ALTER TABLE `".$table."` ADD `document_ppd_amount_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.0' AFTER `document_discount_amount_tax_excl`;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `".$table."` ADD `document_ppd_amount_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.0' AFTER `document_discount_amount_tax_excl`;");

		Illuminate\Support\Facades\DB::statement("ALTER TABLE `".$table."` ADD `document_ppd_percent` DECIMAL(20,6) NOT NULL DEFAULT '0.0' AFTER `document_discount_amount_tax_excl`;");
	}

	die('OK');


	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_quotations` ADD `order_at` DATETIME NULL AFTER `valid_until_date`;");


//	die('OK');


	Illuminate\Support\Facades\DB::statement("CREATE TABLE `help_contents` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `language_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `help_contents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `help_contents_slug_unique` (`slug`);");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `help_contents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;");


	die('OK');


	Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` ADD `volume` DECIMAL(20,6) NOT NULL DEFAULT '0.0' AFTER `depth`;");


	Illuminate\Support\Facades\DB::statement("ALTER TABLE `users` ADD `theme` VARCHAR(128) NULL AFTER `home_page`;");



	die('OK');


	// GM only.

	\Artisan::call('migrate', array('--path' => 'database/migrations/custom', '--force' => true));


	Illuminate\Support\Facades\DB::statement("INSERT INTO `users` (`name`, `email`, `password`, `home_page`, `theme`, `firstname`, `lastname`, `remember_token`, `is_admin`, `active`, `language_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
('Susie', 'susie@gmdistribuciones.es', '\$2y\$10\$DqUj3dvZIMJ4QZk.8SgC6O187AkU.AeloXlWrXoeFaqeCJeEb69.2', '/desktop', 'chinese', 'Yue Mei', 'Li', '6o4rjy8SMrIH5vaQqaszDFs40wH9VZofvuitEoxNfXajzczWqeFhHSIGXjdR', 1, 1, 1, '2019-03-24 10:59:05', '2019-03-24 10:59:05', NULL);");


	die('OK');

	// Mangone only (last):

        \App\Configuration::updateValue('USE_CUSTOM_THEME', 'crmeuropean');


	die('OK');
	

	// Mangone only:

	\App\Configuration::updateValue('ABSRC_ITEMS_PERPAGE', '8') ;


	\App\Configuration::updateValue('ABSRC_HEADER_TITLE', '<span style="color:#bbb"><i class="fa fa-bolt"></i> Lar<span style="color:#fff">aBillander</span> </span>') ;

	\App\Configuration::updateValue('ALLOW_CUSTOMER_RETRO_ORDERS', '0') ;
	
	\App\Configuration::updateValue('ABCC_OUT_OF_STOCK_TEXT', 'Este Producto actualmente no se encuentra en stock.') ;

	\App\Configuration::updateValue('ABCC_OUT_OF_STOCK_PRODUCTS', 'hide') ;


	\App\Configuration::updateValue('ABCC_ENABLE_SHIPPING_SLIPS', '0') ;
	\App\Configuration::updateValue('ABCC_ENABLE_INVOICES', '0') ;
	
	\App\Configuration::updateValue('ABCC_ENABLE_QUOTATIONS', '0') ;
	\App\Configuration::updateValue('ABCC_ENABLE_MIN_ORDER', '0') ;
	\App\Configuration::updateValue('ABCC_MIN_ORDER_VALUE', '100.0') ;
	
	\App\Configuration::updateValue('CUSTOMER_INVOICE_CAPTION', 'Sociedad inscrita en el Registro Mercantil de Ciudad.') ;




        \App\Configuration::updateValue('DEF_CUSTOMER_QUOTATION_TEMPLATE', 108);

        \App\Configuration::updateValue('DEF_CUSTOMER_QUOTATION_SEQUENCE', 4);

        \App\Configuration::updateValue('ABCC_QUOTATIONS_SEQUENCE', 4);

        \App\Configuration::updateValue('DEF_CUSTOMER_ORDER_TEMPLATE', 107);


	die('OK');
	

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `sales_reps` ADD `reference_external` varchar(32) DEFAULT NULL AFTER `notes`;");

	\App\Configuration::updateValue('ABSRC_ITEMS_PERPAGE', '8') ;


	die('OK');


	\App\Configuration::updateValue('ABSRC_HEADER_TITLE', '<span style="color:#bbb"><i class="fa fa-bolt"></i> Lar<span style="color:#fff">aBillander</span> </span>') ;

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` ADD `recommended_retail_price` DECIMAL(20,6) NOT NULL DEFAULT '0' AFTER `cost_average`;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` ADD `recommended_retail_price_tax_inc` DECIMAL(20,6) NOT NULL DEFAULT '0' AFTER `recommended_retail_price`;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` ADD `available_for_sale_date` DATETIME NULL AFTER `out_of_stock`;");


	die('OK');


	Illuminate\Support\Facades\DB::statement("CREATE TABLE `sales_rep_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `language_id` int(10) UNSIGNED NOT NULL,
  `sales_rep_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	Illuminate\Support\Facades\DB::statement("INSERT INTO `sales_rep_users` (`id`, `name`, `email`, `password`, `firstname`, `lastname`, `remember_token`, `active`, `language_id`, `sales_rep_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '', 'dcomobject@hotmail.com', '\$2y\$10\$yhNlf9g4G60algHXlyRLFOzR2aXNhpCgsZri5Z4f7k4AG/r9e7ih6', 'Billy', 'Ray', 'WDrnmvDrlh3NiiylFL3ivdDkzzqhl8clckPEY3W4Sa1Qk38GRfKPRfZ9rKt3', 1, 1, 1, '2019-03-04 23:00:00', '2019-03-04 23:00:00', NULL);");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `sales_rep_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_rep_users_email_unique` (`email`);");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `sales_rep_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");


	die('OK');


	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_orders` ADD `aggregated_at` DATETIME NULL AFTER `close_date`;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_orders` ADD `invoiced_at` DATETIME NULL AFTER `close_date`;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_orders` ADD `shipping_slip_at` DATETIME NULL AFTER `close_date`;");
  
	// Albaranes:

	$as = \App\CustomerOrder::get();

	foreach ($as as $a) {
		# code...
		if ( $a->status == 'closed' )
		{
			//
			if ($a->shippingslip)
			{
				$a->shipping_slip_at = $a->close_date;
				abi_r($a->id);
				$a->save();
			}
		}
	}


	die('OK');

	Illuminate\Support\Facades\DB::statement("CREATE TABLE `customer_quotations` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `sequence_id` int(10) UNSIGNED DEFAULT NULL,
  `document_prefix` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `document_reference` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_customer` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_external` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_via` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT 'manual',
  `document_date` datetime NOT NULL,
  `payment_date` datetime DEFAULT NULL,
  `validation_date` datetime DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `delivery_date_real` datetime DEFAULT NULL,
  `close_date` datetime DEFAULT NULL,
  `document_discount_percent` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `document_discount_amount_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `document_discount_amount_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `number_of_packages` smallint(5) UNSIGNED NOT NULL DEFAULT '1',
  `volume` decimal(20,6) DEFAULT '0.000000',
  `weight` decimal(20,6) DEFAULT '0.000000',
  `shipping_conditions` text COLLATE utf8mb4_unicode_ci,
  `tracking_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_conversion_rate` decimal(20,6) NOT NULL DEFAULT '1.000000',
  `down_payment` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_discounts_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_discounts_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_products_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_products_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_shipping_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_shipping_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_other_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_other_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_lines_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_lines_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_currency_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_currency_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_currency_paid` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `commission_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `notes_from_customer` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `notes_to_customer` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `onhold` tinyint(4) NOT NULL DEFAULT '0',
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  `invoicing_address_id` int(10) UNSIGNED NOT NULL,
  `shipping_address_id` int(10) UNSIGNED DEFAULT NULL,
  `warehouse_id` int(10) UNSIGNED DEFAULT NULL,
  `shipping_method_id` int(10) UNSIGNED DEFAULT NULL,
  `carrier_id` int(10) UNSIGNED DEFAULT NULL,
  `sales_rep_id` int(10) UNSIGNED DEFAULT NULL,
  `currency_id` int(10) UNSIGNED NOT NULL,
  `payment_method_id` int(10) UNSIGNED NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `export_date` datetime DEFAULT NULL,
  `secure_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `import_key` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `valid_until_date` date DEFAULT NULL,
  `prices_entered_with_tax` tinyint(4) NOT NULL DEFAULT '0',
  `round_prices_with_tax` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	Illuminate\Support\Facades\DB::statement("CREATE TABLE `customer_quotation_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `line_sort_order` int(11) DEFAULT NULL,
  `line_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `combination_id` int(10) UNSIGNED DEFAULT NULL,
  `reference` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(20,6) NOT NULL,
  `measure_unit_id` int(10) UNSIGNED NOT NULL,
  `prices_entered_with_tax` tinyint(4) NOT NULL DEFAULT '0',
  `cost_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_customer_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_customer_final_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_customer_final_price_tax_inc` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_final_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_final_price_tax_inc` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `sales_equalization` tinyint(4) NOT NULL DEFAULT '0',
  `discount_percent` decimal(8,3) NOT NULL DEFAULT '0.000',
  `discount_amount_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `discount_amount_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `tax_percent` decimal(8,3) NOT NULL DEFAULT '0.000',
  `commission_percent` decimal(8,3) NOT NULL DEFAULT '0.000',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  `tax_id` int(10) UNSIGNED NOT NULL,
  `sales_rep_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_quotation_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	Illuminate\Support\Facades\DB::statement("CREATE TABLE `customer_quotation_line_taxes` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_rule_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taxable_base` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `percent` decimal(8,3) NOT NULL DEFAULT '0.000',
  `amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_line_tax` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `position` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `tax_id` int(10) UNSIGNED NOT NULL,
  `tax_rule_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_quotation_line_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_quotations`
  ADD PRIMARY KEY (`id`);");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_quotation_lines`
  ADD PRIMARY KEY (`id`);");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_quotation_line_taxes`
  ADD PRIMARY KEY (`id`);");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_quotations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_quotation_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_quotation_line_taxes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;");

//	Illuminate\Support\Facades\DB::statement("");
  
        $t = \App\Template::create( [
//            'id' => 1,
            'name' => 'Plantilla Presupuestos', 
            'model_name' => 'CustomerQuotationPdf', 
            'folder' => 'templates::', 
            'file_name' => 'default', 
            'paper' => 'A4', 
            'orientation' => 'portrait',
        ] );

        \App\Configuration::updateValue('DEF_CUSTOMER_QUOTATION_TEMPLATE', $t->id);

        $t = \App\Sequence::create( [
            'name'    => 'Presupuestos de Clientes', 
            'model_name'    => 'CustomerQuotation', 
            'prefix'    => 'PRE', 
            'length'    => '4', 
            'separator'    => '-', 
            'next_id'     => '1',
            'active'    => '1' ,
        ] );

        \App\Configuration::updateValue('DEF_CUSTOMER_QUOTATION_SEQUENCE', $t->id);

        \App\Configuration::updateValue('ABCC_QUOTATIONS_SEQUENCE', $t->id);
  
	die('OK');
  
        $t = \App\Template::create( [
//            'id' => 1,
            'name' => 'Plantilla Pedidos', 
            'model_name' => 'CustomerOrderPdf', 
            'folder' => 'templates::', 
            'file_name' => 'default', 
            'paper' => 'A4', 
            'orientation' => 'portrait',
        ] );

        \App\Configuration::updateValue('DEF_CUSTOMER_ORDER_TEMPLATE', $t->id);

	die('OK');

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_orders` ADD `backordered_at` DATETIME NULL AFTER `close_date`;");

	Illuminate\Support\Facades\DB::statement("UPDATE `configurations` SET `name` = 'ALLOW_CUSTOMER_BACKORDERS' WHERE `configurations`.`name` = 'ALLOW_CUSTOMER_RETRO_ORDERS';");

	die('OK');

	\App\Configuration::updateValue('ALLOW_CUSTOMER_RETRO_ORDERS', '0') ;

	die('OK');

	Illuminate\Support\Facades\DB::statement("CREATE TABLE `document_ascriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `leftable_id` int(11) NOT NULL,
  `leftable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rightable_id` int(11) NOT NULL,
  `rightable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'traceability',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `document_ascriptions`
  ADD PRIMARY KEY (`id`);");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `document_ascriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;");

	die('OK');

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_orders` ADD `onhold` TINYINT(4) NOT NULL DEFAULT '0' AFTER `status`;");

	die('OK');
	
	$ds = \App\CustomerInvoice::get();

	abi_r($ds->count());

	foreach ($ds as $d) {
		# code...
		if ($d->created_via == 'aggregate') $d->update(['created_via' => 'aggregate_shipping_slips']);
	}

	die('OK');

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_shipping_slips` ADD `invoiced_at` DATE NULL AFTER `production_sheet_id`;");

	die('OK');

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_shipping_slips` ADD `onhold` TINYINT(4) NOT NULL DEFAULT '0' AFTER `status`;");

	die('OK');

	Illuminate\Support\Facades\DB::statement("DROP TABLE `customer_shipping_slips`, `customer_shipping_slip_lines`, `customer_shipping_slip_line_taxes`;");

	Illuminate\Support\Facades\DB::statement("CREATE TABLE `customer_shipping_slips` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `sequence_id` int(10) UNSIGNED DEFAULT NULL,
  `document_prefix` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `document_reference` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_customer` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_external` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_via` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT 'manual',
  `document_date` datetime NOT NULL,
  `payment_date` datetime DEFAULT NULL,
  `validation_date` datetime DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `delivery_date_real` datetime DEFAULT NULL,
  `close_date` datetime DEFAULT NULL,
  `document_discount_percent` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `document_discount_amount_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `document_discount_amount_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `number_of_packages` smallint(5) UNSIGNED NOT NULL DEFAULT '1',
  `volume` decimal(20,6) DEFAULT '0.000000',
  `weight` decimal(20,6) DEFAULT '0.000000',
  `shipping_conditions` text COLLATE utf8mb4_unicode_ci,
  `tracking_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_conversion_rate` decimal(20,6) NOT NULL DEFAULT '1.000000',
  `down_payment` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_discounts_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_discounts_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_products_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_products_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_shipping_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_shipping_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_other_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_other_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_lines_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_lines_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_currency_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_currency_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_currency_paid` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `commission_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `notes_from_customer` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `notes_to_customer` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  `invoicing_address_id` int(10) UNSIGNED NOT NULL,
  `shipping_address_id` int(10) UNSIGNED DEFAULT NULL,
  `warehouse_id` int(10) UNSIGNED DEFAULT NULL,
  `shipping_method_id` int(10) UNSIGNED DEFAULT NULL,
  `carrier_id` int(10) UNSIGNED DEFAULT NULL,
  `sales_rep_id` int(10) UNSIGNED DEFAULT NULL,
  `currency_id` int(10) UNSIGNED NOT NULL,
  `payment_method_id` int(10) UNSIGNED NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `export_date` datetime DEFAULT NULL,
  `secure_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `import_key` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_document_id` int(10) UNSIGNED DEFAULT NULL,
  `production_sheet_id` int(10) UNSIGNED DEFAULT NULL,
  `printed_at` date DEFAULT NULL,
  `edocument_sent_at` date DEFAULT NULL,
  `customer_viewed_at` date DEFAULT NULL,
  `prices_entered_with_tax` tinyint(4) NOT NULL DEFAULT '0',
  `round_prices_with_tax` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	Illuminate\Support\Facades\DB::statement("CREATE TABLE `customer_shipping_slip_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `line_sort_order` int(11) DEFAULT NULL,
  `line_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `combination_id` int(10) UNSIGNED DEFAULT NULL,
  `reference` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(20,6) NOT NULL,
  `measure_unit_id` int(10) UNSIGNED NOT NULL,
  `prices_entered_with_tax` tinyint(4) NOT NULL DEFAULT '0',
  `cost_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_customer_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_customer_final_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_customer_final_price_tax_inc` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_final_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unit_final_price_tax_inc` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `sales_equalization` tinyint(4) NOT NULL DEFAULT '0',
  `discount_percent` decimal(8,3) NOT NULL DEFAULT '0.000',
  `discount_amount_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `discount_amount_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `tax_percent` decimal(8,3) NOT NULL DEFAULT '0.000',
  `commission_percent` decimal(8,3) NOT NULL DEFAULT '0.000',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  `tax_id` int(10) UNSIGNED NOT NULL,
  `sales_rep_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_shipping_slip_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	Illuminate\Support\Facades\DB::statement("CREATE TABLE `customer_shipping_slip_line_taxes` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_rule_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taxable_base` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `percent` decimal(8,3) NOT NULL DEFAULT '0.000',
  `amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `total_line_tax` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `position` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `tax_id` int(10) UNSIGNED NOT NULL,
  `tax_rule_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_shipping_slip_line_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_shipping_slips`
  ADD PRIMARY KEY (`id`);");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_shipping_slip_lines`
  ADD PRIMARY KEY (`id`);");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_shipping_slip_line_taxes`
  ADD PRIMARY KEY (`id`);");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_shipping_slips`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_shipping_slip_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_shipping_slip_line_taxes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;");

	die('OK');

	Illuminate\Support\Facades\DB::statement("CREATE TABLE `price_rules` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `combination_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_group_id` int(10) UNSIGNED DEFAULT NULL,
  `currency_id` int(10) UNSIGNED DEFAULT NULL,
  `rule_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'discount',
  `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `discount_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `discount_percent` decimal(8,3) NOT NULL DEFAULT '0.000',
  `discount_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `discount_amount_is_tax_incl` tinyint(4) NOT NULL DEFAULT '0',
  `from_quantity` decimal(20,6) NOT NULL,
  `date_from` datetime DEFAULT NULL,
  `date_to` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `price_rules`
  ADD PRIMARY KEY (`id`);");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `price_rules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;");

	die('OK');
	
	\App\Configuration::updateValue('ABCC_OUT_OF_STOCK_TEXT', 'Este Producto actualmente no se encuentra en stock.') ;

	\App\Configuration::updateValue('ABCC_OUT_OF_STOCK_PRODUCTS', 'hide') ;

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` ADD `out_of_stock` VARCHAR(32) NOT NULL DEFAULT 'default' AFTER `phantom_assembly`;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` ADD `out_of_stock_text` TEXT NULL AFTER `out_of_stock`;");

	die('OK');

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `stock_movements` DROP `measure_unit_id`;");

	die('OK');

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `stock_count_lines` CHANGE `cost_price` `cost_price` DECIMAL(20,6) NULL;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `stock_movements` ADD `measure_unit_id` INT(10) UNSIGNED NULL AFTER `quantity`;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `stock_movements` ADD `price_currency` DECIMAL(20,6) NULL AFTER `price`;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_invoices` ADD `stock_status` VARCHAR(32) NOT NULL DEFAULT 'pending' AFTER `payment_status`;");

	die('OK');


	\App\Configuration::updateValue('ABCC_ENABLE_SHIPPING_SLIPS', '0') ;
	\App\Configuration::updateValue('ABCC_ENABLE_INVOICES', '0') ;

	die('OK');

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_users` ADD `enable_quotations` TINYINT(4) NOT NULL DEFAULT '-1' AFTER `active`;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_users` ADD `enable_min_order` TINYINT(4) NOT NULL DEFAULT '-1' AFTER `enable_quotations`;");

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_users` ADD `min_order_value` DECIMAL(20,6) NOT NULL DEFAULT '0.0' AFTER `enable_min_order`;");
	
	\App\Configuration::updateValue('ABCC_ENABLE_QUOTATIONS', '0') ;
	\App\Configuration::updateValue('ABCC_ENABLE_MIN_ORDER', '0') ;
	\App\Configuration::updateValue('ABCC_MIN_ORDER_VALUE', '100.0') ;

	die('OK');
	
	\App\Configuration::updateValue('CUSTOMER_INVOICE_CAPTION', 'Sociedad inscrita en el Registro Mercantil de Ciudad.') ;

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_invoices` ADD `onhold` TINYINT(4) NOT NULL DEFAULT '0' AFTER `status`;");

	die('OK');

//	Illuminate\Support\Facades\DB::statement('ALTER TABLE products ADD FULLTEXT fulltext_index (name, reference, ean13, description)');

	Illuminate\Support\Facades\DB::statement("ALTER TABLE `customer_invoices` ADD `payment_status` VARCHAR(32) NOT NULL DEFAULT 'pending' AFTER `type`;");

	 abi_r('OK');

});


/* ********************************************************** */


if (file_exists(__DIR__.'/confluence_gus.php')) {
    include __DIR__.'/confluence_gus.php';
}

/* ********************************************************** */




/* ********************************************************** */

