@extends('layouts.layouts')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Agent Payment</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="/home">Home</a>
                </li>
                <li>
                    <a>Agent</a>
                </li>
                <li class="active">
                    <strong>Payment</strong>
                </li>
            </ol>
        </div>

        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="#">Config option 1</a>
                                    </li>
                                    <li><a href="#">Config option 2</a>
                                    </li>
                                </ul>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <table class="table table-striped table-bordered table-hover dataTables-example" >
                                <div class="col-lg-12">

                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                        @if (session('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('agent.payment.confirmation') }}">
                                        @csrf
                                    <div class="form-group">

                                        <div align="center" class="col-sm-10 col-lg-12">

                                            <div class="form-group @error('user_name') has-error @enderror">
                                                <label class="col-sm-2 control-label">Agent Username</label>
                                                <div class="col-sm-10">
                                                <div class="input-group m-b"><span class="input-group-addon">@</span> <input type="text" name="user_name" placeholder="Enter Agent Username" class="form-control @error('username') is-invalid @enderror"></div>
                                                    @error('user_name')
                                                    <div class="alert alert-danger alert-dismissable">
                                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <button class="btn btn-primary" type="submit">Lookup</button>

                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </table>

                            @if($alist ?? '')
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Note</th>
                                        <th>Wallet</th>
                                        <th>Date</th>
                                        <th>Username</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $data)
                                        <tr>
                                            <td>{{$data->id}}</td>
                                            <td>{{$data->note}}</td>
                                            <td>{{$data->wallet}}</td>
                                            <td>{{$data->reg_date}}</td>
                                            <td>{{$data->user_name}}</td>
                                            <td>
                                                <form method="POST" action="{{ route('agent.payment.confirmation') }}">
                                                    <input type="hidden" name="user_name" value="{{$data->user_name}}">
                                                    <button class="btn btn-success" type="submit">Continue</button>
                                                @csrf
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif

                        </div>
                    </div>
                </div>

                @if($val ?? '')
                <div class="col-lg-6">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Transactions</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="#">Config option 1</a>
                                    </li>
                                    <li><a href="#">Config option 2</a>
                                    </li>
                                </ul>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Username</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($trans as $data)
                                <tr>
                                    <td>{{$data->id}}</td>
                                    <td>{{$data->description}}</td>
                                    <td>{{$data->status}}</td>
                                    <td>{{$data->amount}}</td>
                                    <td>{{$data->date}}</td>
                                    <td>{{$data->user_name}}</td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Agent Payment</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="#">Config option 1</a>
                                    </li>
                                    <li><a href="#">Config option 2</a>
                                    </li>
                                </ul>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <form method="POST" action="{{ route('agent.payment') }}">
                                @csrf
                                <div class="form-group">

                                    <div align="center" class="col-sm-10 col-lg-12">

                                        <input type="hidden" name="user_name" value="{{$user->user_name}}" placeholder="Enter Agent Username" class="form-control @error('username') is-invalid @enderror">

                                        <div class="form-group @error('count') has-error @enderror">
                                                <label class="col-sm-2 control-label">Count</label>
                                                <div class="col-sm-10">
                                                    <div class="input-group m-b"><span class="input-group-addon">#</span> <input type="number" name="count" value="{{$count}}" placeholder="Enter record count you want payment for" class="form-control @error('count') is-invalid @enderror" required></div>
                                                    @error('count')
                                                    <div class="alert alert-danger alert-dismissable">
                                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                        </div>

                                        <button class="btn btn-primary" type="submit">Process Payment</button>

                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                @endif

            </div>
        </div>

    </div>

            @endsection
