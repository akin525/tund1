@extends('layouts.layouts')

@section('content')


<div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>User List</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index.html">Home</a>
                        </li>
                        <li>
                            <a>User</a>
                        </li>
                        <li class="active">
                            <strong>User List</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="wrapper wrapper-content">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-success pull-right">All Time</span>
                                <h5>Total Users</h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins">{{number_format($t_users)}}</h1>
                                <div class="stat-percent font-bold text-success">{{round(($ac_users/$t_users) * 100)}}% <i class="fa fa-bolt"></i></div>
                                <small>Active Users : {{number_format($ac_users)}} | Inactive Users : {{number_format($iac_users)}} | Fraud Users : {{number_format($f_users)}}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-success pull-right">All Time</span>
                                <h5>Total Referred</h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins">{{number_format($r_users)}}</h1>
{{--                                <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>--}}
{{--                                <small>Total Referred</small>--}}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-success pull-right">Monthly</span>
                                <h5>Total Agent</h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins">{{number_format($a_users)}}</h1>
{{--                                <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>--}}
{{--                                <small>Active Wallet: {{number_format($au_wallet)}}</small>--}}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-success pull-right">All Time</span>
                                <h5>Total Wallet</h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins">#{{number_format($u_wallet)}}</h1>
                                <div class="stat-percent font-bold text-success">{{round((($au_wallet/$u_wallet) * 100))}} <i class="fa fa-bolt"></i></div>
                                <small>Active Wallet: {{number_format($au_wallet)}} | Inactive Wallet: {{number_format($iau_wallet)}} | Fraud Wallet: {{number_format($fu_wallet)}}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Users</h5>
                        </div>
                        <div class="ibox-content">
                            <p>

                                <div class="text-center">
                           </div>

                            <table class="table table-striped table-bordered table-hover dataTables-example" >
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>wallet</th>
                                    <th>phoneno</th>
                                    <th>status</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                            @foreach($users as $user)
                                    <tr class="gradeX">
                                        <td>{{$user->id}}</td>
                                        <td>{{$user->user_name }}
                                        <td>{{$user->full_name }}
                                        <td>{{$user->email }}</td>
                                        <td>{{$user->wallet}}</td>
                                        <td>{{$user->phoneno}}</td>
                                        <td class="center">{{$user->status}}</td>
                                        <td class="center"><a data-toggle="modal" class="btn btn-primary" href="profile/{{ $user->user_name }}">View</a></td>
                                    </tr>
                                @endforeach

                                </tbody>
                                <tfoot>
                                <tr>

                                    <th>id</th>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>wallet</th>
                                    <th>phoneno</th>
                                    <th>status</th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>

        @endsection
