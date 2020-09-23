@extends('layouts.layouts')
@section('title', 'Wallet List')
@section('parentPageTitle', 'Wallet')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Wallet Table</h4>
                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>Username</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Medium</th>
                                <th>Reference</th>
                                <th>O. Wallet</th>
                                <th>N. Wallet</th>
                                <th>Version</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $dat)
                                <tr>
                                    <td>{{$dat->id}}</td>
                                    <td>{{$dat->user_name}}</td>
                                    <td>&#8358;{{$dat->amount}}</td>
                                    <td>{{$dat->status}}</td>
                                    <td>{{$dat->medium}}</td>
                                    <td>{{$dat->ref}}</td>
                                    <td>&#8358;{{$dat->o_wallet}}</td>
                                    <td>&#8358;{{$dat->n_wallet}}</td>
                                    <td>{{$dat->version}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
