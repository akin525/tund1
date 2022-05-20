@extends('layouts.layouts')
@section('title', 'User-Role')
@section('parentPageTitle', 'Role')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            <strong>{{ session('success') }}</strong>
                        </div>
                        <script type="text/javascript">
                            toastr.options = {
                                closeButton: true,
                                progressBar: true,
                                showMethod: 'slideDown',
                                timeOut: 4000
                            };
                            toastr.success('{{ session('success') }}', 'Success');
                        </script>
                    @endif
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
                            @foreach($user as $da)
                                <form method="post" action="{{route('updaterole')}}">
                                    @csrf
                                    <td>{{$da['id']}}</td>
                                    <input type="hidden" name="id" value="{{$da['id']}}">
                                    <td>{{$da['user_name']}}</td>
                                    <td class="center">
                                        <select name="status">
                                            <option value="{{$da['status']}}">{{$da['status']}}</option>
                                            <option value="superadmin">Superadmin</option>
                                            <option value="client">Client</option>
                                            <option value="reseller">Reseller</option>
                                            <option value="agent">Agents</option>
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
                    {{$user->links()}}

                </div>
            </div>
        </div>
    </div>
@endsection
