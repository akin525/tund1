@extends('layouts.layouts')
@section('title', 'Reseller Airtime Control')
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
                    <p class="text-muted mb-4 font-13">Network List</p>
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Network</th>
                            <th>Level1</th>
                            <th>Level2</th>
                            <th>Level3</th>
                            <th>Level4</th>
                            <th>Level5</th>
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
                                <td class="center">{{$da['network']}}</td>
                                <td>{{$da['level1']}}</td>
                                <td>{{$da['level2']}}</td>
                                <td>{{$da['level3']}}</td>
                                <td>{{$da['level4']}}</td>
                                <td>{{$da['level5']}}</td>
                                <td class="center">{{$da['server']}}</td>
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
                                    <a class="btn {{$da->status =="1"? "btn-gradient-danger" : "btn-success" }}" href="{{route('reseller.airtimecontrolED',$da->id)}}">
                                        {{$da->status =="1"? "Disable" : "Enable" }}
                                    </a>
                                    <a href="{{route('reseller.airtimecontrolEdit',$da->id )}}"  class="btn btn-secondary">Modify</a>
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
