@extends('layouts.layouts')
@section('title', 'Payment Gateway')
@section('parentPageTitle', 'User')

@section('content')
    <div class="content-body">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="row page-breadcrumbs">
                                <div class="col-md-12 align-self-center">
                                    <h4 class="theme-cl">Update Payment-Gateway</h4>
                                </div>
                            </div>


                            <form class="form-horizontal" action="{{route('updategate')}}" method="post">
                                @csrf
                                @if(isset($mes))
                                    <div class='alert alert-success'>
                                        <button type='button' class='close' data-dismiss='alert'>&times;</button>
                                        <i class='fa fa-ban-circle'></i><strong>Success: </br></strong><b>{{$mes}}</b>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label class="mb-1"><strong>Payment Name</strong></label>

                                    <input type="text" class="form-control" name="name" value="{{$payment->name}}"
                                           required>
                                    <input type="hidden" class="form-control" name="id" value="{{$payment->id}}"
                                           required>

                                </div>

                                <div class="form-group">
                                    <label class="mb-1"><strong>Value</strong></label>
                                    <input type="text" name="va" class="form-control" value="{{$payment->value}}"
                                           required>
                                </div>
                                <div class="form-group">
                                    <div class="flex-box align-items-center">
                                        <button type="submit" class="btn btn-rounded btn-outline-success btn-block">
                                            Update Details
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
