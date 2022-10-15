@extends('layouts.layouts')
@section('title', 'Data Plans')
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
                    <p class="text-muted mb-4 font-13">Data Plans</p>

                        <div class="row ml-3 mb-3">
                            <a href="{{route('dataserveMultipleedit', [$data[0]->network, 'ALL', $cg == 1 ? 0 : 1 ])}}" class="btn btn-secondary mr-3">@if($all == 1)Disable @else Enable @endif All Data</a>
                            <a href="{{route('dataserveMultipleedit', [$data[0]->network, 'CG', $cg == 1 ? 0 : 1 ])}}" class="btn btn-secondary mr-3">@if($cg == 1)Disable @else Enable @endif CG Data</a>
                            <a href="{{route('dataserveMultipleedit', [$data[0]->network, 'SME', $sme == 1 ? 0 : 1])}}" class="btn btn-secondary mr-3">@if($sme == 1)Disable @else Enable @endif SME Data</a>
                            <a href="{{route('dataserveMultipleedit', [$data[0]->network, 'DG', $dg == 1 ? 0 : 1 ])}}" class="btn btn-secondary mr-3">@if($dg == 1)Disable @else Enable @endif DG Data</a>
                            <a href="{{route('datanew')}}" class="btn btn-gradient-success mr-3">Create New Data Plan</a>
                        </div>

                    <table class="table table-striped table-bordered table-hover dataTables-example table-responsive">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Network</th>
                            <th>Product Name</th>
                            <th>Provider Price</th>
                            <th>Your Price</th>
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

                                <td>{{$da['name']}}</td>
                                <td class="center">{{$da['price']}}</td>
                                <td class="center">{{$da['pricing']}}</td>
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
                                    <a class="btn {{$da->status =="1"? "btn-gradient-danger" : "btn-success" }}" href="{{route('dataserveED',$da->id)}}">
                                         {{$da->status =="1"? "Disable" : "Enable" }}
                                    </a>
                                    <a href="{{route('datacontrolEdit',$da->id )}}"  class="btn btn-secondary">Modify</a>
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
