@extends('layouts.layouts')
@section('title', 'Resellers')
@section('parentPageTitle', 'User')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
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

                    <p class="text-muted mb-4 font-13">The list of approved resellers.</p>
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Business Name</th>
                            <th>DOB</th>
                            <th>Phone Number</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                @if($user->photo!=null)
                                    <img alt="image" class="rounded-circle thumb-sm mr-1" src="https://mcd.5starcompany.com.ng/app/avatar/{{$user->user_name }}.JPG">
                                @else
                                    <img alt="image" class="rounded-circle thumb-sm mr-1" src="/img/mcd_logo.png">
                                @endif
                                {{$user->user_name }}</td>
                            <td>{{$user->company_name }}</td>
                            <td>{{$user->dob }}</td>
                            <td>{{$user->phoneno}}</td>
                            <td>
                                <a href="profile/{{ $user->user_name }}" class="btn btn-sm btn-success"><i class="fas fa-eye"></i> View</a>
                                <a href="{{route('regenerateKey', $user->id)}}" class="btn btn-sm btn-danger"><i class="fas fa-recycle"></i> Regenerate Key</a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
