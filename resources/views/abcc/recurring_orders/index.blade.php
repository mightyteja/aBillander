@extends('abcc.layouts.master')

@section('title') {{ l('My Recurring Orders') }} @parent @stop

@section('content')

    <div class="page-header">
        <div class="pull-right">

            <a class="btn btn-grey" href="{{ route('abcc.recurringorders.create') }}"
               title="{{l('New recurring order', [], 'layouts')}}" style="margin-right: 72px">
                <i class="fa fa-plus"></i> {{l('Create new recurring order', [], 'layouts')}}
            </a>
        </div>
        <h2>
            {{ l('My Recurring Orders') }}
        </h2>
    </div>

    <div id="div_customer_recurring_orders">

        <div class="table-responsive">

            @if ($customer_recurring_orders->count())
                <table id="customer_recurring_orders" class="table table-hover">
                    <thead>
                    <tr>
                        <th class="text-left">{{ l('Order #') }}</th>
                        <th class="text-left">{{ l('Start at Date') }}</th>
                        <th class="text-left">{{ l('Next Occurring at Date') }}</th>
                        <th class="text-left">{{ l('Frequency') }}</th>
                        <th class="text-left">{{ l('Active') }}</th>
                        <th class="text-center">{{ l('Notes') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="order_lines">
                    @foreach ($customer_recurring_orders as $recurring_order)
                        <tr>
                            <td>{{ $recurring_order->id }} / {{ $recurring_order->customer_order_id }}</td>
                            <td>{{ abi_date_form_full($recurring_order->start_at) }}</td>
                            <td>{{ abi_date_form_full($recurring_order->next_occurring_at) }}</td>
                            <td>{{ $recurring_order->frequency }}</td>
                            <td>{{ $recurring_order->active }}</td>

                            <td class="text-right">
                            <!--<a class="btn btn-sm btn-grey" href="{{ route('abcc.order.pdf', [$recurring_order->id]) }}"
                                   title="{{l('PDF Export', [], 'layouts')}}" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
                                <a class="btn btn-sm btn-warning" href="{{ route('abcc.order.duplicate', [$recurring_order->id]) }}"
                                   title="{{l('Copy Order to Cart')}}"><i class="fa fa-copy"></i></a>-->
                                <a class="btn btn-sm btn-warning" href="{{ route('abcc.recurringorders.edit', [$recurring_order->id]) }}"
                                   title="{{l('View', [], 'layouts')}}"><i class="fa fa-pencil"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

        </div><!-- div class="table-responsive" ENDS -->

        {{ $customer_recurring_orders->appends( Request::all() )->render() }}
        <ul class="pagination">
            <li class="active">
                <span style="color:#333333;">{{l('Found :nbr record(s)', [ 'nbr' => $customer_recurring_orders->total() ], 'layouts')}} </span>
            </li>
        </ul>

        @else
            <div class="alert alert-warning alert-block">
                <i class="fa fa-warning"></i>
                {{l('No records found', [], 'layouts')}}
            </div>
        @endif

    </div><!-- div id="div_customer_recurring_orders" ENDS -->

@endsection

{{-- *************************************** --}}


@section('scripts') @parent

<script>

    // check box selection -->
    // See: http://www.dotnetcurry.com/jquery/1272/select-deselect-multiple-checkbox-using-jquery

    $(function () {
        var $tblChkBox = $("#order_lines input:checkbox");
        $("#ckbCheckAll").on("click", function () {
            $($tblChkBox).prop('checked', $(this).prop('checked'));
        });
    });

    $("#order_lines").on("change", function () {
        if (!$(this).prop("checked")) {
            $("#ckbCheckAll").prop("checked", false);
        }
    });

</script>

@endsection


{{-- *************************************** --}}


@section('scripts') @parent

{{-- Date Picker --}}

<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
{!! HTML::script('assets/plugins/jQuery-UI/datepicker/datepicker-'.\App\Context::getContext()->language->iso_code.'.js'); !!}

<script>

    $(function () {
        $("#due_date").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: "{{ \App\Context::getContext()->language->date_format_lite_view }}"
        });
    });

</script>

@endsection




@section('styles') @parent

{{-- Date Picker --}}

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<style>
    .ui-datepicker { z-index: 10000 !important; }
</style>

@endsection
