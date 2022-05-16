@extends('layouts.layouts')
@section('title', 'Sever Controller')
@section('parentPageTitle', 'Sever Controller')

@section('content')
    <div class="content-body">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="row page-breadcrumbs">
                                <div class="col-md-12 align-self-center">
                                    <h4 class="theme-cl">Update Airtime Server</h4>
                                </div>
                            </div>


                            {{--                            <div class="form-group">--}}
                            {{--                                <div class="contact-thumb">--}}
                            {{--                                    <img width="100" src="{{asset('images/avater.jpg')}}" class="img-circle img-responsive" alt="">--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}


                            <form class="form-horizontal" action="{{route('updateairtimeserver')}}" method="post">
                                @csrf
                                @if(isset($success))
                                    <div class='alert alert-success'>
                                        <button type='button' class='close' data-dismiss='alert'>&times;</button>
                                        <i class='fa fa-ban-circle'></i><strong>Success: </br></strong><b>{{$success}}</b>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label class="mb-1"><strong>Select Product</strong></label>

                                    <select name="network" class="form-control" required="">
                                        <option value="mtn">MTN</option>
                                        <option value="glo">GLO</option>
                                        <option value="airtel">AIRTEL</option>
                                        <option value="etisalat">9MOBILE</option>

                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="mb-1"><strong>Select Server</strong></label>

                                    <select name="number" class="form-control" required="">
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
                                </div>


                                <div class="form-group">
                                    <div class="flex-box align-items-center">
                                        <button type="submit" class="btn btn-rounded btn-outline-success btn-block">
                                            Update Server
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


