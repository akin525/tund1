@extends('layouts.layouts')
@section('title', 'CG Bundle List')
@section('parentPageTitle', 'CG Bundle')

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
{{--                    <a href="{{route('sliders.create')}}" class="btn btn-primary mb-3 text-white">Add New Slider</a>--}}
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @foreach($data as $da)
                                <td>{{$da['id']}}</td>
                                <td class="center">{{$da['display_name']}}</td>
                                <td>{{$da['value']}} GB</td>
                                <td class="center">{{$da['network']}} {{$da['type']}}</td>
                                <td class="center">{{$da['price']}}</td>
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
                                    <a href="{{route('cgbundle.edit',$da->id )}}" class="btn btn-primary">Edit</a>
                                    @if($da->status == 1)
                                        <a href="{{route('cgbundle.modify',$da->id )}}" class="btn btn-warning">Disable</a>
                                    @else
                                        <a href="{{route('cgbundle.modify',$da->id )}}" class="btn btn-success">Enable</a>
                                    @endif
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
