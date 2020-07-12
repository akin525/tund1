@extends('layouts.layouts')

@section('content')
<div class="wrapper wrapper-content">
        <div class="row">
                    <div class="col-lg-3 col-md-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-success pull-right">All Time</span>
                                <h5>Users</h5>
                            </div>
                            <div class="ibox-content">
                                <h3 class="no-margins">{{ $total_user ?? 'Active User Calculated' }} | {{ $active_user ?? 'Active User Calculated' }} | {{ $inactive_user ?? 'Inactive User Calculated' }}</h3>
{{--                                <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>--}}
                                <small>Total | Active | Inactive</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-info pull-right">Current</span>
                                <h5>Transactions</h5>
                            </div>
                            <div class="ibox-content">
                                <h3 class="no-margins">{{ $total_transaction ?? 'Total Transactions Calculated' }} | {{ $successful_transaction ?? 'Total Transactions Calculated' }} | {{ $fail_transaction ?? 'Total Transactions Calculated' }}</h3>
{{--                                <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i></div>--}}
                                <small>Total | Successful | Unsuccessful</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-primary pull-right">Today</span>
                                <h5>Deposit</h5>
                            </div>
                            <div class="ibox-content">
                                <h3 class="no-margins">{{ $today_deposits ?? 'Today Deposits' }} | {{ $total_deposits ?? 'Total Calculated' }}</h3>
{{--                                <div class="stat-percent font-bold text-navy">44% <i class="fa fa-level-up"></i></div>--}}
                                <small>Today Deposits | Total Deposits</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-danger pull-right">All Time</span>
                                <h5>User Segment</h5>
                            </div>
                            <div class="ibox-content">
                                <h3 class="no-margins">{{ $client ?? 'Online User Calculated' }} | {{ $agent ?? 'Online User Calculated' }} | {{ $reseller ?? 'Online User Calculated' }}</h3>
{{--                                <div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>--}}
                                <small>Clients | Agents | Resellers</small>
                            </div>
                        </div>
            </div>
        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>Payment Channels</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link">
                                                <i class="fa fa-chevron-up"></i>
                                            </a>
                                            <a class="close-link">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        <table class="table table-hover no-margins">
                                            <thead>
                                            <tr>
                                                <th>Channel</th>
                                                <th>Total Count</th>
                                                <th>Today Count</th>
                                                <th>%</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr> <?php $total=$banktransfer + $paystack + $payant + $rave; ?>
                                                <td><small class="label" style="background-color: #995566; color: white">Bank Transfer</small></td>
                                                <td><i class="fa fa-clock-o"></i> {{$banktransfer ?? ''}}</td>
                                                <td>{{$banktransfer_today ?? ''}}</td>
                                                <td class="text-navy"> <i class="fa fa-level-up"></i> {{ round(($banktransfer/$total) * 100, 2) }} </td>
                                            </tr>
                                            <tr>
                                                <td><a class="label" style="background-color: #3333ff; color: white">Paystack</a> </td>
                                                <td><i class="fa fa-clock-o"></i> {{$paystack ?? ''}}</td>
                                                <td>{{$paystack_today ?? ''}}</td>
                                                <td class="text-navy"> <i class="fa fa-level-up"></i> {{ round(($paystack/$total) * 100, 2) }} </td>
                                            </tr>
                                            <tr>
                                                <td><a class="label" style="background-color: #000044; color: white">Payant</a> </td>
                                                <td><i class="fa fa-clock-o"></i> {{$payant ?? ''}}</td>
                                                <td>{{$payant_today ?? ''}}</td>
                                                <td class="text-navy"> <i class="fa fa-level-up"></i> {{ round(($payant/$total) * 100, 2) }} </td>
                                            </tr>
                                            <tr>
                                                <td><a class="label label-warning">Rave</a> </td>
                                                <td><i class="fa fa-clock-o"></i> {{$rave ?? ''}}</td>
                                                <td>{{$rave_today ?? ''}}</td>
                                                <td class="text-navy"> <i class="fa fa-level-up"></i> {{ round(($rave/$total ) * 100, 2) }} </td>
                                            </tr>
                                            <tr style="font-weight: bolder;">
                                                <td>Total </td>
                                                <td><i class="fa fa-clock-o"></i> {{$total ?? ''}}</td>
                                                <td></td>
                                                <td class="text-navy"> <i class="fa"></i> 100% </td>
                                            </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>Payment todo list</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link">
                                                <i class="fa fa-chevron-up"></i>
                                            </a>
                                            <a class="close-link">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        <ul class="todo-list m-t small-list">
                                            @foreach($walletlogs as $data)
                                                @if($data->status=='successful')
                                            <li>
                                                <a href="#" class="check-link"><i class="fa fa-check-square"></i> </a>
                                                <span class="m-l-xs todo-completed">User Name: {{$data->user_name}}; Channel: {{$data->medium}}; Amount: {{$data->amount}}; </span>
                                                <small class="label label-primary"><i class="fa fa-clock-o"></i> {{\Carbon\Carbon::parse($data->date)->toFormattedDateString() }} </small>
                                            </li>
                                                @elseif($data->status=='cancelled')
                                            <li>
                                                <a href="#" class="check-link"><i class="fa fa-check-o"></i> </a>
                                                <span class="m-l-xs todo-completed">User Name: {{$data->user_name}}; Channel: {{$data->medium}}; Amount: {{$data->amount}};</span>
                                                <small class="label label-danger"><i class="fa fa-clock-o"></i> {{\Carbon\Carbon::parse($data->date)->toFormattedDateString() }} </small>
                                            </li>
                                                @else
                                            <li>
                                                <a href="#" class="check-link"><i class="fa fa-square-o"></i> </a>
                                                <span class="m-l-xs">User Name: {{$data->user_name}}; Channel: {{$data->medium}}; Amount: {{$data->amount}};</span>
                                                <small class="label label-warning"><i class="fa fa-clock-o"></i> {{\Carbon\Carbon::parse($data->date)->toFormattedDateString() }} </small>
                                            </li>
                                                @endif
                                                    @endforeach

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>Recent Transactions</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link">
                                                <i class="fa fa-chevron-up"></i>
                                            </a>
                                            <a class="close-link">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ibox-content">

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <table class="table table-hover margin bottom">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 1%" class="text-center">Ip</th>
                                                        <th>Transaction</th>
                                                        <th class="text-center">Date</th>
                                                        <th class="text-center">Amount</th>
                                                        <th class="text-center">Prev. Bal</th>
                                                        <th class="text-center">Bal.</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($transactions as $trans)
                                                    <tr>
                                                        <td class="text-center">{{$trans->ip_address}}</td>
                                                        <td> {{$trans->description}} </td>
                                                        <td class="text-center small">{{$trans->date}}</td>
                                                        @if ($trans->status =="delivered" || $trans->status =="successful" )
                                                        <td class="text-center"><span class="label label-primary">{{$trans->amount}}</span></td>
                                                        @elseif ($trans->status =="not_delivered" || $trans->status =="cancelled")
                                                            <td class="text-center"><span class="label label-warning">#{{$trans->amount}}</span></td>
                                                        @endif
                                                        <td class="text-center">{{$trans->i_wallet}}</td>
                                                        <td class="text-center">{{$trans->f_wallet}}</td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>

        </div>
@endsection
