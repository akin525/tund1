@extends('layouts.layouts')
@section('title', 'User-Role')
@section('parentPageTitle', 'Role')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="general-label">

                        <p class="text-muted mb-4 font-13">Add User to Admin</p>

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

                        <form class="form-horizontal" method="POST" action="{{route('updaterole')}}">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-12">

                                    <div class="input-group mt-2">
                                        <select class="custom-select form-control" name="id">
                                            @foreach($userlist as $user)
                                                <option value="{{$user->id}}">{{$user->user_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="input-group mt-2">
                                        <select class="custom-select form-control" name="status">
                                            <option value="superadmin">Superadmin</option>
                                            <option value="admin">admin</option>
                                        </select>
                                    </div>

                                    <div class="input-group mt-2" style="align-content: center">
                                        <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center">Add to Admin</button>
                                    </div>

                                </div>
                            </div>
                            <!--end row-->
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-4 font-13">Users Role</p>
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Username</th>
                            <th>Assigned Role</th>
                            <th>Update</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @foreach($admins as $da)
                                <form method="post" action="{{route('updaterole')}}">
                                    @csrf
                                    <td>{{$i++}}</td>
                                    <input type="hidden" name="id" value="{{$da['id']}}">
                                    <td>{{$da['user_name']}}</td>
                                    <td class="center">
                                        <select class="custom-select form-control" name="status">
                                            <option value="{{$da['status']}}">{{$da['status']}}</option>
                                            <option value="superadmin">Superadmin</option>
                                            <option value="admin">admin</option>
                                            <option value="client">Downgrade</option>
                                        </select>
                                    </td>
                                    <td class="center">
                                        <button type="submit" class="btn btn-outline-primary">Update</button>

                                    </td>

                        </tr>
                        </form>
                        @endforeach
                        </tbody>
                    </table>
{{--                    {{$user->links()}}--}}

                </div>
            </div>
        </div>
    </div>
@endsection
