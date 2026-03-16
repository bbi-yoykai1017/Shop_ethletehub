<?php 

?>
<!DOCTYPE html>
<html>

<head>

    <title>login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container mt-3 p-2 my-5 bg-dark text-white" style="text-align: center;">

        <ul class="nav justify-content-center">
            <li class="nav-item">
                <a class="nav-link" style="color: white;">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" style="color: white;">Đăng nhập</a>
            </li>
            <li class="nav-item" >
                <a class="nav-link" style="color: aqua;">Đăng ký</a>
            </li>

        </ul>
    </div>
   <div class="container" style="display: flex; justify-content: center; padding-top: 50px;" >
    <div id="loginbox" class="col-md-6 col-sm-8" style="border: 2px solid black ;">
        <div class="panel panel-info"  >
            <div class="panel-heading">
                <div class="panel-title text-center mt-3"><h3>Màn hình đăng nhập</h3></div>
            </div>

            <div style="padding: 30px" class="panel-body">
                <form method="post" class="form-horizontal">
                    <div class="input-group" style="margin-bottom: 20px">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user" style="margin-right: 20px;">Username</i></span>
                        <input id="login-username" type="text" class="form-control" name="username" >
                    </div>

                    <div class="input-group" style="margin-bottom: 20px">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock" style="margin-right: 20px;">Mật khẩu</i></span>
                        <input id="login-password" type="password" class="form-control" >
                    </div>
                    <div class="margin-bottom-25">
                            <input type="checkbox" tabindex="3" class="" name="remember" id="remember">
                            <label for="remember"> Ghi nhớ đăng nhập</label>
                        </div>
                    <div class="form-group text-center">
                        <button class="btn btn-default" style="color:blue; background-color: aliceblue;">Quên mật khẩu</button>
                        <a id="btn-fblogin" href="#" class="btn btn-primary">Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <footer>
        <div class="container mt-3 p-3 my-5 bg-dark text-white" style="text-align: center;">
            <span>Lập trình backend wed 2</span>
        </div>
    </footer>

</body>

</html>