@extends('layouts.layouts')
@section('title', 'Data-Control')
@section('parentPageTitle', 'Change-Server')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(isset($status))
                        <h6 class="alert alert-danger">{{$status}}</h6>
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
                                <td>{{$da['id']}}</td>
                                <td>{{$da['name']}}</td>
                                <td>{{$da['coded']}}</td>
                                <td class="center">
                                    <select name="sel">
                                        <option value="{{$da['server']}}">{{$da['server']}}</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">9</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </td>
                                <td class="center">{{$da['product_code']}}</td>
                                <td class="center">{{$da['price']}}</td>
                                <td class="center">{{$da['network']}}</td>
                                <td class="center">
                                    <button type="button" class="btn btn-outline-primary">Update</button>
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
