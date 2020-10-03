@extends('layouts.layouts')
@section('title', 'Verification > Server3 ')
@section('parentPageTitle', 'Transaction')

@section('content')

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="general-label">

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

                    <form class="form-horizontal" method="POST" action="{{ route('verification_server3') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">REF</span></div>
                                    <input type="text" name="ref" placeholder="Enter server reference" class="form-control @error('ref') is-invalid @enderror" required>
                                    <button class="btn btn-gradient-primary waves-effect waves-light" type="submit" style="align-self: center; align-content: center"><i class="fa fa-search"></i>Verify</button>
                                </div>
                                @error('ref')
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    {{ $message }}
                                </div>
                                @enderror

                                <td class="border-0">Custom HTML description and buttons</td>
                                <td class="border-0"><button type="button" class="btn btn-primary waves-effect waves-light" id="custom-html-alert">Click me</button></td>
                            </div>
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($val ?? '')
        <div class="row">

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <p>Transaction</p>
                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Username</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$data->id}}</td>
                                        <td>{{$data->description}}</td>
                                        <td>{{$data->amount}}</td>
                                        <td>{{$data->user_name}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <p>Reversal</p>
                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Username</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rtran as $tran)
                                    <tr>
                                        <td>{{$tran->id}}</td>
                                        <td>Being reversal of {{$tran->description}}</td>
                                        <td>{{$tran->amount}}</td>
                                        <td>{{$tran->user_name}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
            <a href="/reverse-transaction/{{$data->id}}" class="btn btn-gradient-danger waves-effect waves-light" type="submit"><i class="mdi mdi-alert-outline mr-2"></i>Reverse Transaction</a>

        </div>
    @endif
    <!-- end row -->
@endsection
