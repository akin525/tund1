@extends('layouts.layouts')
@section('title', 'FAQ List')
@section('parentPageTitle', 'FAQ')

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
{{--                    <a href="{{route('sliders.create')}}" class="btn btn-primary mb-3 text-white">Add New FAQ</a>--}}
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Name</th>
                            <th>Content</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @foreach($data as $da)
                                <td>{{$da['id']}}</td>
                                <td class="center">{{$da['name']}}</td>
                                <td><img src="{{route('show.sliders',$da['image'])}}" class="img img-thumbnail" /></td>
                                <td class="center">{{$da['action']}}</td>
                                <td class="center">
                                    @if($da->status=="1")
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    {{$da['created_at']}}</option>
                                </td>

                                <td class="center">
                                    @if($da->status == 1)
                                        <a href="{{route('sliders.update',$da->id )}}" class="btn btn-warning">Disable</a>
                                    @else
                                        <a href="{{route('sliders.update',$da->id )}}" class="btn btn-success">Enable</a>
                                    @endif

                                    <a href="{{route('sliders.delete',$da->id )}}" class="btn btn-danger">Remove</a>
                                </td>

                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection
