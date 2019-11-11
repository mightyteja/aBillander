<div class="row">
    <div class="form-group col-lg-12 col-md-12 col-sm-12">
        {!! Form::label('name', l('Name')) !!}
        {{ Form::text('name', $recurring_order->name, array('class' => 'form-control')) }}
        {{ Form::hidden('customer_order_id', $recurring_order->customer_order_id) }}

    </div>
</div>
<div class="row">

    <div class="form-group col-lg-4 col-md-4 col-sm-4">
        {!! Form::label('start_at_form',l('Start At')) !!}
        {!! Form::text('start_at_form', date('d/m/Y', strtotime($recurring_order->start_at)), array('id' => 'start_at_form', 'class' => 'form-control')) !!}
    </div>

    <div class="form-group col-lg-4 col-md-4 col-sm-4">
        {!! Form::label('next_at_form', l('Next At')) !!}
        {!! Form::text('next_at_form', date('d/m/Y', strtotime($recurring_order->next_at)),
            array('id' => 'next_at_form', 'class' => 'form-control', 'disabled' => true)) !!}
    </div>
    <div class="form-group col-lg-4 col-md-4 col-sm-4">
        {!! Form::label('end_at_form', l('End At')) !!}
        {!! Form::text('end_at_form', date('d/m/Y', strtotime($recurring_order->end_at)), array('id' => 'end_at_form', 'class' => 'form-control')) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-lg-4 col-md-4 col-sm-4">
        {!! Form::label('frequency', l('Frequency in days')) !!}
        {!! Form::text('frequency', $recurring_order->frequency ?: 7, array('class' => 'form-control', 'id' => 'frecuency')) !!}
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

<div class="row">
    <div class="form-group col-lg-12 col-md-12 col-sm-12">
        {!! Form::label('notes', l('Notes')) !!}
        {!! Form::textarea('notes', null, ['id' => 'notes', 'rows' => 4, 'cols' => 54, 'style' => 'resize:none', 'class' => 'form-control']) !!}
    </div>
</div>

{!! Form::submit(l('Save', [], 'layouts'), array('class' => 'btn btn-success')) !!}
{!! link_to_route('recurringorders.index', l('Cancel', [], 'layouts'), null, array('class' => 'btn btn-warning')) !!}


@section('styles')    @parent
    {{-- Date Picker --}}
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
@endsection

@section('scripts')    @parent

<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
{!! HTML::script('assets/plugins/jQuery-UI/datepicker/datepicker-'.\App\Context::getContext()->language->iso_code.'.js'); !!}

<script type="text/javascript">
    $(document).ready(function () {

        $("#start_at_form").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: "{{ \App\Context::getContext()->language->date_format_lite_view }}"
        });

        $("#end_at_form").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: "{{ \App\Context::getContext()->language->date_format_lite_view }}"
        });
    });
</script>
@endsection
