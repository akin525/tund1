@extends('layouts.layouts')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Add General News</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="/home">Home</a>
                </li>
                <li>
                    <a>User</a>
                </li>
                <li class="active">
                    <strong>General News</strong>
                </li>
            </ol>
        </div>

        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="#">Config option 1</a>
                                    </li>
                                    <li><a href="#">Config option 2</a>
                                    </li>
                                </ul>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <table class="table table-striped table-bordered table-hover dataTables-example" >
                                <div class="col-lg-12">

                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('addgnews') }}">
                                        @csrf
                                    <div class="form-group">

                                        <div align="center" class="col-sm-10 col-lg-12">

                                            <div class="form-group @error('username') has-error @enderror">
                                                <label class="col-sm-2 control-label">Username</label>
                                                <div class="col-sm-10">
                                            <div class="input-group m-b"><span class="input-group-addon">@</span> <input type="text" name="user_name" placeholder="Enter Username (Optional)" class="form-control @error('username') is-invalid @enderror"></div>
                                                    @error('username')
                                                    <div class="alert alert-danger alert-dismissable">
                                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Message</label>
                                                <div class="col-sm-10">
                                                    <textarea name="message" class="form-control" style="margin-bottom: 12px"> </textarea>
                                                </div>
                                            </div>


                                            <div class="form-group">

                                            <button class="btn btn-primary" type="submit">Broadcast</button>
                                            </div>

                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

            @endsection