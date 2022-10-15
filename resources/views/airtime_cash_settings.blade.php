@extends('layouts.layouts')
@section('title', 'Airtime Converter')
@section('parentPageTitle', 'Settings')

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

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Reference</th>
                                                    <th>Amount</th>
                                                    <th>Network</th>
                                                    <th>Phone Number</th>
                                                    <th>Status</th>
                                                    <th>Username</th>
                                                    <th>Receiver</th>
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
                                                        <td>
                                                            @if($data->status=="successful")
                                                                <span class="badge badge-success">{{$data->status}}</span>
                                                            @else
                                                                <span class="badge badge-warning">{{$data->status}}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{$data->user_name}}</td>
                                                        <td>{{$data->receiver}}</td>
                                                        <td>{{$data->created_at}}</td>
                                                        <td>{{$data->version}}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                            {{ $datas->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->
                        </div>


                </div>
            </div>
        </div>
    </div>


@endsection
