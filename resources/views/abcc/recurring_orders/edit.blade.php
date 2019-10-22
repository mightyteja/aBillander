@extends('abcc.layouts.master')

@section('title') {{ l('Customer Recurring Order - Show') }} @parent @stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <div class="pull-right">

                    <a href="{{ URL::to('abcc/recurringorders') }}" class="btn btn-default">
                        <i class="fa fa-mail-reply"></i> {{l('Back to Recurring Orders')}}
                    </a>
                </div>
                <h2>
                    <a href="{{ route('abcc.recurringorders.index') }}">{{l('Recurring Order Detail', [], 'abcc/layouts')}}</a> &nbsp;
                    <span style="color: #cccccc;">/</span>

                    <span class="badge" style="background-color: #3a87ad; margin-right: 72px;"
                          title="{{ '' }}">{{ $recurring_order->customerOrder->currency->iso_code }}</span></h2>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-md-offset-3" style="margin-top: 50px">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {{ l('Edit Recurring Order') }} :: ({{$recurring_order->id}}) Original Order {{$recurring_order->customerOrder->id}}
                        </h3>
                    </div>
                    <div class="panel-body">

                        @include('errors.list')

                        {!! Form::model($recurring_order, array('method' => 'PATCH', 'route' => array('abcc.recurringorders.update', $recurring_order->id))) !!}

                        @include('abcc.recurring_orders._form')

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
