@extends('layouts.layouts')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-9">
            <h2>Resellers</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="/">Home</a>
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
                <div class="col-lg-4" style="margin-bottom: 5px">
                    <div class="contact-box">
                        <a href="profile/{{ $user->user_name }}">
                            <div class="col-sm-4">
                                <div class="text-center">
                                    @if($user->photo!=null)
                                        <img alt="image" class="img-circle m-t-xs img-responsive" src="https://mcd.5starcompany.com.ng/app/avatar/{{$user->user_name }}.JPG">
                                    @else
                                        <img alt="image" class="img-circle m-t-xs img-responsive" src="/img/mcd_logo.png">
                                    @endif
                                        <div class="m-t-xs font-bold">{{$user->user_name }}</div>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <h3><strong>{{$user->full_name }}</strong></h3>
                                <p><i class="fa fa-calendar-o"></i> {{$user->dob }}</p>
                                <address>
                                    <strong><i class="fa fa-briefcase"></i> {{$user->company_name }}</strong><br>
                                    <i class="fa fa-map-marker"></i> {{$user->address }}<br>
                                    <abbr title="Phone"><i class="fa fa-mobile-phone"></i>:</abbr> {{$user->phoneno}}<br />
                                    <abbr title="Note"><i class="fa fa-book"></i>:</abbr> {{$user->note}}
                                </address>
                            </div>
                            <div class="clearfix"></div>
                        </a>
                    </div>
                </div>

            @endforeach

        </div>
    </div>

        @endsection
