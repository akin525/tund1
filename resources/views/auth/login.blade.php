<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mega Cheap Data | Login</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

        <!-- Toastr style -->
    <link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">

        <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
        <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>
            <!-- Toastr script -->
    <script src="js/plugins/toastr/toastr.min.js"></script>


</head>

<body class="gray-bg">

    <div class="middle-box text-center loginscreen  animated fadeInDown">
        <div>
            <p></p>
            <div class="animated lightSpeedIn">
                 <img class="img img-responsive" src="img/mcd_logo.png" />

            </div>
            <h3>Welcome to Mega Cheap Data</h3>
            <p>...the cheapest you can always get
            </p>
            <p>Login strictly for Admins</p>
            <form method="POST" action="{{ route('login') }}">
                        @csrf
                <div class="form-group">

                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback text-danger " role="alert">
                                    </span>
                                    <script type="text/javascript">
                                        toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };
                                        toastr.error('{{ $message }}', 'Login Error' );</script>
                                @enderror
                </div>
                <div class="form-group">

                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    <script type="text/javascript">toastr.error('{{ $message }}', 'Login Error' );</script>
                                @enderror
                </div>
                <div class="form-group">

                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"  {{ old('remember') ? 'checked' : '' }} >

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b">{{ __('Login') }}</button>

            </form>
            <p class="m-t"> <small>Powered by 5Star Company &copy; 2019</small> </p>
        </div>
    </div>

</body>

</html>
