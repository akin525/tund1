@extends('layouts.layouts')
@section('title', 'Data-Control')
@section('parentPageTitle', 'Change-Server')

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
                    <p class="text-muted mb-4 font-13">Data-Servercontroller</p>
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Product Name</th>
                            <th>Product coded</th>
                            <th>Server</th>
                            <th>Product Code</th>
                            <th>Price</th>
                            <th>Network</th>
                            <th>Update</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @foreach($data as $da)
                                <form method="post" action="{{route('datacontrol1')}}">
                                    @csrf
                                    <td>{{$da['id']}}</td>
                                    <input type="hidden" name="id" value="{{$da['id']}}">
                                    <td>{{$da['name']}}</td>
                                    <td>{{$da['coded']}}</td>
                                    <td class="center">
                                        <select name="number">
                                            <option value="{{$da['server']}}">{{$da['server']}}</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>
                                        </select>
                                    </td>
                                    <td class="center">{{$da['product_code']}}</td>
                                    <td class="center">{{$da['price']}}</td>
                                    <td class="center">{{$da['network']}}</td>
                                    <td class="center">
                                        <button type="submit" class="btn btn-outline-primary">Update</button>

                                    </td>

                        </tr>
                        </form>
                        @endforeach
                        </tbody>
                    </table>
                    {{$data->links()}}

                </div>
            </div>
        </div>
    </div>
@endsection
