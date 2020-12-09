@extends('layouts.layouts')
@section('title', 'Profit And Loss')
@section('parentPageTitle', 'Reports')

@section('content')

    <div class="row">
    <div class="col-lg-4">
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

                    <form class="form-horizontal" method="POST" action="{{ route('finduser') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    </div>
                                    <input style="margin-right: 20px" type="text" name="user_name" placeholder="Search for username" class="form-control @error('user_name') is-invalid @enderror">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-mobile"></i> </span>
                                    </div>
                                    <input type="tel" name="phoneno" placeholder="Search for phone number" class="form-control @error('phoneno') is-invalid @enderror">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-users"></i> </span>
                                    </div>
                                    <input style="margin-right: 20px" type="text" name="status" placeholder="Search User group e.g agent, client, reseller" class="form-control @error('status') is-invalid @enderror">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-wallet"></i></span>
                                    </div>
                                    <input type="number" name="wallet" placeholder="Search for wallet value" class="form-control @error('wallet') is-invalid @enderror">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i> </span>
                                    </div>
                                    <input style="margin-right: 20px" type="email" name="email" placeholder="Search for email address" class="form-control @error('email') is-invalid @enderror">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-calendar-check"></i> </span>
                                    </div>
                                    <input type="date" name="regdate" placeholder="Search for registration date e.g 2020-09-01" class="form-control @error('regdate') is-invalid @enderror">
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center"><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($income ?? '')
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mt-0 header-title">Profit & Loss Report</h4>
                        <p class="text-muted mb-4 font-13"></p>
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($income as $come)
                                    <tr>
                                        <td>{{$come->gl}}</td>
                                        <td>{{$come->amount}}</td>
                                    </tr>
                                    <?php
                                    $ti += $come->amount;
                                    ?>
                                @endforeach
                                Total Income: {{$ti+$come->amount}}
                                </tbody>
                            </table>

                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($expenses as $exp)
                                    <tr>
                                        <td>{{$exp->gl}}</td>
                                        <td>{{$exp->amount}}</td>
                                    </tr>
                                    <?php
                                    $te += $exp->amount;
                                        ?>
                                @endforeach
                                Total Expenses: {{$te}}
                                </tbody>
                            </table>

                            Net Income: {{$ti - $te}}
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
    @endif
    </div>
    <!-- end row -->
@endsection
