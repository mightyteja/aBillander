<div class="row">
    <div class="form-group col-lg-4 col-md-4 col-sm-4">
        {!! Form::label('customer_order_id', l('Original Order')) !!}
        {!! Form::select('customer_order_id', $customer_orders, null, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="row">

    <div class="form-group col-lg-6 col-md-6 col-sm-6">
        {!! Form::label('next_occurring_at', l('Start At')) !!}
        {{ Form::input('dateTime-local', 'start_at', date('Y-m-d\TH:i', strtotime($recurring_order->next_occurring_at)), array('class' => 'form-control')) }}
    </div>
    <div class="form-group col-lg-6 col-md-6 col-sm-6">
        {!! Form::label('next_occurring_at', l('Next occurring At')) !!}
        {{ Form::input('dateTime-local', 'start_at', date('Y-m-d\TH:i', strtotime($recurring_order->next_occurring_at)), array('class' => 'form-control')) }}
    </div>
</div>

<div class="row">
    <div class="form-group col-lg-4 col-md-4 col-sm-4">
        {!! Form::label('frequency', l('Frequency')) !!}
        {!! Form::text('frequency', $recurring_order->frequency, array('class' => 'form-control', 'id' => 'frecuency')) !!}
    </div>

    <div class="form-group col-lg-4 col-md-4 col-sm-4" id="div-active">
        {!! Form::label('active', l('Active?', [], 'layouts'), ['class' => 'control-label']) !!}
        <div>
            <div class="radio-inline">
                <label>
                    {!! Form::radio('active', '1', true, ['id' => 'active_on']) !!}
                    {!! l('Yes', [], 'layouts') !!}
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    {!! Form::radio('active', '0', false, ['id' => 'active_off']) !!}
                    {!! l('No', [], 'layouts') !!}
                </label>
            </div>
        </div>
    </div>
</div>

{!! Form::submit(l('Save', [], 'layouts'), array('class' => 'btn btn-success')) !!}
{!! link_to_route('abcc.recurringorders.index', l('Cancel', [], 'layouts'), null, array('class' => 'btn btn-warning')) !!}


@section('scripts')    @parent

@endsection
