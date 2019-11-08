@extends('layouts.master')

@section('title') {{ l('Customer Recurring Order - Create') }} @parent @stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <div class="pull-right">

                    <a href="{{ URL::to('recurringorders') }}" class="btn btn-default">
                        <i class="fa fa-mail-reply"></i> {{l('Back to Recurring Orders')}}
                    </a>
                </div>
                <h2>
                    <a href="{{ route('recurringorders.index') }}">{{l('Recurring Order Detail', [], 'layouts')}}</a> &nbsp;
                    <span style="color: #cccccc;">/</span>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-md-offset-3" style="margin-top: 50px">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {{ l('Create Recurring Order') }}
                        </h3>
                    </div>
                    <div class="panel-body">

                        @include('errors.list')

                        {!! Form::model($recurring_order, array('method' => 'POST', 'route' => array('recurringorders.store'))) !!}

                        @include('customer_recurring_orders._form')

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
