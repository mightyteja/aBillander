<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\DeliveryRoute;
use App\DeliveryRouteLine;

class DeliveryRoutesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deliveryroutes = DeliveryRoute::orderBy('id', 'asc')->get();

        return view('delivery_routes.index', compact('deliveryroutes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('delivery_routes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, DeliveryRoute::$rules);

        $deliveryroute = DeliveryRoute::create($request->all());

        return redirect('deliveryroutes')
                ->with('info', l('This record has been successfully created &#58&#58 (:id) ', ['id' => $deliveryroute->id], 'layouts') . $request->input('name'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DeliveryRoute  $deliveryroute
     * @return \Illuminate\Http\Response
     */
    public function show(DeliveryRoute $deliveryroute)
    {
        return $this->edit($deliveryroute);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DeliveryRoute  $deliveryroute
     * @return \Illuminate\Http\Response
     */
    public function edit(DeliveryRoute $deliveryroute)
    {
        return view('delivery_routes.edit', compact('deliveryroute'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DeliveryRoute  $deliveryroute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeliveryRoute $deliveryroute)
    {
        $this->validate($request, DeliveryRoute::$rules);

        $deliveryroute->update($request->all());

        return redirect('deliveryroutes')
                ->with('success', l('This record has been successfully updated &#58&#58 (:id) ', ['id' => $deliveryroute->id], 'layouts') . $request->input('name'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DeliveryRoute  $deliveryroute
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryRoute $deliveryroute)
    {
        $id = $deliveryroute->id;

        $deliveryroute->delete();

        return redirect('deliveryroutes')
                ->with('success', l('This record has been successfully deleted &#58&#58 (:id) ', ['id' => $id], 'layouts'));
    }


    /**
     * AJAX Stuff.
     *
     * 
     */
    public function sortLines(Request $request)
    {
        $positions = $request->input('positions', []);

        \DB::transaction(function () use ($positions) {
            foreach ($positions as $position) {
                DeliveryRouteLine::where('id', '=', $position[0])->update(['line_sort_order' => $position[1]]);
            }
        });

        return response()->json($positions);
    }
}
