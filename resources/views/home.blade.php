@extends('layouts.layouts')
@section('title', 'Home')
@section('parentPageTitle', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-tasks text-gradient-success"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $today_transaction ?? 'Today Transactions Calculated' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Transactions</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body justify-content-center">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="far fa-gem text-gradient-danger"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $p_nd_l ?? 'p and l' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Charges</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-users text-gradient-warning"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $today_user ?? 'Active User Calculated' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Users</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i
                                            class="fas fa-database text-gradient-primary"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $today_deposits ?? 'Today Deposits' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Deposits</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-briefcase text-success"></i>
                                    </div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $data ?? 'Today Data' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Data</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-briefcase text-success"></i>
                                    </div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $airtime ?? 'airtime' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Airtime</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-briefcase text-success"></i>
                                    </div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $tv ?? 'tv' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's CableTv</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-briefcase text-success"></i>
                                    </div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $betting ?? 'Today Betting' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Betting</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="btn-group btn-group-toggle float-right" data-toggle="buttons"><label
                            class="btn btn-primary btn-sm active"><input type="radio" name="options" id="option1"
                                                                         checked=""> This Week</label> <label
                            class="btn btn-primary btn-sm"><input type="radio" name="options" id="option2"> Last
                            Month</label></div>
                    <h5 class="header-title mb-4 mt-0">Weekly Record</h5>
                    <canvas id="lineChar" height="82"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown d-inline-block float-right">
                        <a class="nav-link dropdown-toggle arrow-none" id="dLabel4" data-toggle="dropdown" href="#"
                           role="button" aria-haspopup="false" aria-expanded="false"><i
                                class="fas fa-ellipsis-v font-20 text-muted"></i></a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel4"><a
                                class="dropdown-item" href="#">Create Project</a> <a class="dropdown-item" href="#">Open
                                Project</a> <a class="dropdown-item" href="#">Tasks Details</a></div>
                    </div>
                    <h5 class="header-title mb-4 mt-0">Service Status</h5>
                    <div>
                        @foreach($allsettings as $settings)
                            <div>{{$settings->name}}
                                    @if($settings->value)
                                        <i class="mdi mdi-label text-success mr-2">On</i>
                                    @else
                                        <i class="mdi mdi-label text-danger mr-2">Off</i>
                                    @endif
                            </div>
                        @endforeach
                    </div>
                    <ul class="list-unstyled list-inline text-center mb-0 mt-3">
                        <li class="mb-2 list-inline-item text-muted font-13"><i class="mdi mdi-label text-success mr-2"></i>Active</li>
                        <li class="mb-2 list-inline-item text-muted font-13"><i class="mdi mdi-label text-danger mr-2"></i>Disabled</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown d-inline-block float-right">
                        <a class="nav-link dropdown-toggle arrow-none" id="dLabel5" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"><i class="fas fa-ellipsis-v font-20 text-muted"></i></a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel5"><a class="dropdown-item" href="#">New Messages</a> <a class="dropdown-item" href="#">Open Messages</a></div>
                    </div>
                    <h5 class="header-title pb-3 mt-0">New Clients</h5>
                    <div class="table-responsive boxscroll" style="overflow: hidden; outline: none;">
                        <table class="table mb-0">
                            <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td class="border-top-0">
                                    <div class="media">
                                        @if($user->photo)
                                            <img src="https://mcd.5starcompany.com.ng/app/avatar/{{$user->photo}}" alt="user" class="thumb-md rounded-circle">
                                        @else
                                            <img src="img/mcd_logo.png" alt="" class="thumb-md rounded-circle">
                                        @endif
                                        <div class="media-body ml-2">
                                            <p class="mb-0">{{$user->user_name}} <span class="badge badge-soft-primary">{{$user->status}}</span></p>
                                            <span class="font-12 text-muted">{{$user->reg_date}}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="border-top-0 text-right"><a href="#" class="btn btn-light btn-sm"><i class="far fa-user mr-2 text-success"></i>View</a></td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card">
                <div class="card-body">
{{--                    <a href="#" class="btn btn-outline-success float-right">&#8358; {{$general_market->value}}</a>--}}
                    <h5 class="header-title mb-4 mt-0">General Market Revenue</h5>
                    <h4 class="mb-4">&#8358; {{$general_market->value}}</h4>
{{--                    <p class="font-14 text-muted mb-4"><i class="mdi mdi-message-reply text-danger mr-2 font-18"></i> $ 1500 when an unknown printer took a galley.</p>--}}
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex align-self-center">
                        <img src="assets/images/widgets/code.svg" alt="" class="" height="100">
                        <div class="media-body ml-3">
                            <h6>Quarterly Target</h6>
                            <p class="text-muted font-13">Get up to active 20k users this quarter</p>
{{--                            <a href="#" class="btn btn-gradient-secondary">Confirm</a>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card timeline-card">
                <div class="card-body p-0">
                    <div class="bg-gradient2 text-white text-center py-3 mb-4">
                        <p class="mb-0 font-18"><i class="mdi mdi-clock-outline font-20"></i> Admin Activities</p>
                    </div>
                </div>
                <div class="card-body boxscroll">
                    <div class="timeline">
                        @foreach($audit_trails as $audit)
                        <div class="entry">
                            <div class="title">
                                <h6>{{\Carbon\Carbon::parse($audit->created_at)->toFormattedDateString()}}</h6>
                            </div>
                            <div class="body">
                                <p>{{$audit->subject}} - <a href="#" class="text-primary">{{$audit->admin_id}}</a></p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="header-title pb-3 mt-0">Recent Transactions</h5>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                            <tr class="align-self-center">
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Server</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transactions as $trans)
                                <tr>
                                    <td>{{$trans->name}}</td>
                                    <td>{{$trans->description}}</td>
                                    <td>
                                        @if ($trans->status =="delivered" || $trans->status =="successful" )
                                            <span class="badge badge-boxed badge-soft-success">{{$trans->status}}</span>
                                        @elseif ($trans->status =="not_delivered" || $trans->status =="cancelled")
                                            <span class="badge badge-boxed badge-soft-warning">{{$trans->status}}</span>
                                        @else
                                            <span class="badge badge-boxed badge-soft-warning">{{$trans->status}}</span>
                                        @endif
                                    </td>
                                    <td>{{\Carbon\Carbon::parse($trans->date)->toFormattedDateString()}}</td>
                                    <td>&#8358;{{number_format($trans->amount)}}</td>
                                    <td>{{$trans->server}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!--end table-responsive-->
                    <div class="pt-3 border-top text-right"><a href="/transaction" class="text-primary">View all <i class="mdi mdi-arrow-right"></i></a></div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@stop

@section('before-scripts')
    <script>
        (gradientStroke1 = (ctx = document.getElementById("lineChar").getContext("2d")).createLinearGradient(0, 0, 0, 300)).addColorStop(0, "#008cff"), gradientStroke1.addColorStop(1, "rgba(22, 195, 233, 0.1)"), gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300), gradientStroke2.addColorStop(0, "#ec536c"), gradientStroke2.addColorStop(1, "rgba(222, 15, 23, 0.1)");
        var myChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: ["6days Ago", "5days Ago", "4days Ago", "3days Ago", "2days Ago", "Yesterday", "Today"],
                datasets: [{
                    label: "Transactions",
                    data: [{{$d6_transaction}}, {{$d5_transaction}}, {{$d4_transaction}}, {{$d3_transaction}}, {{$d2_transaction}}, {{$yesterday_transaction}}, {{$today_transaction}}],
                    pointBorderWidth: 0,
                    pointHoverBackgroundColor: gradientStroke1,
                    backgroundColor: gradientStroke1,
                    borderColor: "transparent",
                    borderWidth: 1
                }]
            },
            options: {
                legend: {
                    position: "bottom",
                    display: 1
                },
                tooltips: {
                    displayColors: 1,
                    intersect: 1
                },
                elements: {
                    point: {
                        radius: 0
                    }
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            max: 100,
                            min: 20,
                            stepSize: 10
                        },
                        gridLines: {
                            display: 1,
                            color: "#FFFFFF"
                        },
                        ticks: {
                            display: 1,
                            fontFamily: "'Rubik', sans-serif"
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            color: "#fff",
                            display: 1
                        },
                        ticks: {
                            display: 1,
                            fontFamily: "'Rubik', sans-serif"
                        }
                    }]
                }
            }
        });
    </script>
@stop
