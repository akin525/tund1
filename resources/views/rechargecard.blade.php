@extends('layouts.layouts')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-9">
            <h2>Resellers</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="index.html">Home</a>
                </li>
                <li>
                    Users
                </li>
                <li class="active">
                    <strong>Resellers</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            @foreach($users as $user)
                <div class="col-lg-4" style="margin-bottom: 1px">
                    <div class="contact-box">
                        <a href="users/{{ $user->id }}">

                            <div class="col-sm-12">
                                <div class="text-center">
                                    <div class="m-t-xs font-bold"><img alt="image" class="img-circle m-t-xs img-responsive pull-left" width="20px" height="20px" src="https://mcd.5starcompany.com.ng/app/avatar/{{$user->user_name }}.JPG"> {{$user->company_name }}</div>
                                    <i class="fa fa-mobile-phone"></i>: {{$user->phoneno}}<br />
                                </div>
                                <strong></strong>
                                <div class="well">
                                        <strong>Card Pin:  83742387876538</strong><br>
                                        Serial No: 2-14947893893
                                    <div class="pull-right">
                                        <a class="btn btn-xs btn-white"><i class="fa fa-thumbs-up"></i> Airtel #100 </a>
                                    </div>
                                </div>
                                <div class="text-center">
                                <span>Powered by Mega Cheap Data</span>
                                </div>

                            </div>
                            <div class="clearfix"></div>
                        </a>
                    </div>
                </div>

            @endforeach

        </div>
    </div>

        @endsection
