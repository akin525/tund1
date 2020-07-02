@extends('layouts.layouts')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Airtime 2 Cash</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="/home">Home</a>
                </li>
                <li>
                    <a>Airtime</a>
                </li>
                <li class="active">
                    <strong>Airtime 2 Cash</strong>
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

                                    <form method="POST" action="{{ route('transaction.airtime2cash.payment') }}">
                                        @csrf
                                    <div class="form-group">

                                        <div align="center" class="col-sm-10 col-lg-12">

                                            <div class="form-group @error('ref') has-error @enderror">
                                                <label class="col-sm-2 control-label">Reference</label>
                                                <div class="col-sm-10">
                                                <div class="input-group m-b"><span class="input-group-addon">#</span> <input type="text" name="ref" placeholder="Enter Reference Number" class="form-control @error('ref') is-invalid @enderror" required></div>
                                                    @error('ref')
                                                    <div class="alert alert-danger alert-dismissable">
                                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <button class="btn btn-primary" type="submit">Credit Wallet</button>
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
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Network</th>
                                        <th>Phone Number</th>
                                        <th>Status</th>
                                        <th>Username</th>
                                        <th>Date</th>
                                        <th>App Version</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($datas as $data)
                                        <tr>
                                            <td>{{$data->id}}</td>
                                            <td>{{$data->ref}}</td>
                                            <td>{{$data->amount}}</td>
                                            <td>{{$data->network}}</td>
                                            <td>{{$data->phoneno}}</td>
                                            <td>{{$data->status}}</td>
                                            <td>{{$data->user_name}}</td>
                                            <td>{{$data->created_at}}</td>
                                            <td>{{$data->version}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

            @endsection
