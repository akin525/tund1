@extends('layouts.layouts')
@section('title', 'TV Plans')
@section('parentPageTitle', 'Services')

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
                    <p class="text-muted mb-4 font-13">TV Plans</p>
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Server</th>
                            <th>Status</th>
                            <th>Date Modified</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @foreach($data as $da)
                                <td>{{$da['id']}}</td>
                                <td class="center">{{$da['type']}}</td>

                                <td>{{$da['name']}}</td>
                                <td class="center">{{$da['price']}}</td>
                                <td class="center">{{$da['discount']}}</td>
                                <td>

                                    {{$da['server']}}</option>

                                </td>
                                <td class="center">
                                    @if($da->status=="1")
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </td>

                                <td>

                                    {{$da['updated_at']}}</option>

                                </td>


                                <td class="center">
                                    <a class="btn {{$da->status =="1"? "btn-gradient-danger" : "btn-success" }}" href="{{route('tvcontrolED',$da->id)}}">
                                        {{$da->status =="1"? "Disable" : "Enable" }}
                                    </a>
                                    <a href="{{route('tvcontrolEdit',$da->id )}}"  class="btn btn-secondary">Modify</a>
                                </td>

                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$data->links()}}

                </div>
            </div>
        </div>
    </div>
@endsection
