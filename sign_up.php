<?php
date_default_timezone_set('PRC');
try {
    $pdo = new PDO('mysql:host=localhost;dbname=blog','root','root');
    $pdo->exec('SET NAMES UTF8');
}
catch(Exception $e) {
    echo '<h1>数据库连接错误！</h1>';
    return;
}

$sql = $pdo->prepare('SELECT * FROM userinfo;');
$sql->execute();
$userinfo = $sql->fetchall(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>注册页面</title>
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script>
        function judge(x) {
            var judge;
            <?php
            foreach($userinfo as $list0) { ?>
            var username=<?php echo '"'.$list0['username'].'"';?>;
            if(x === username) {
                return true;
            }
            <?php
            } ?>
        }
        function check() {
            var username=document.getElementById("username").value;
            if(username === "") {
                document.getElementById("00").innerHTML="请输入用户名";
            }
            else if(judge(username) === true && username != "") {
                document.getElementById("00").innerHTML="该用户名已被注册";
            }
            else {
                document.getElementById("00").innerHTML="该用户名可用";
            }
        }
        function show() {
            if(document.getElementById("password").type === "password") {
                document.getElementById("password").type = "text";
                document.getElementById("btn1").innerHTML = "隐藏密码";
            }
            else {
                document.getElementById("password").type = "password";
                document.getElementById("btn1").innerHTML = "显示密码";
            }
        }
        </script>
    </head>
    <body>
        <form action="sign_up.php" method="POST">
            <div class="container-fluid">
                <br>
                <div class="row" style="font-size: 120%">
                    <div class="col-md-5"></div>
                    <div class="col-md-2">用户名</div>
                    <div class="col-md-5"></div>
                </div>
                <div class="row">
                    <div class="col-md-5"></div>
                    <div class="col-md-2">
                        <input class="form-control" type="text" name="username" id="username" required>
                    </div>
                    <div class="col-md-5">
                        <button id="btn0" type="button" class="btn btn-success" onclick="check()">检查用户名</button>
                        <span id="00"></span>
                    </div>
                </div>
                <br>
                <div class="row" style="font-size: 120%">
                    <div class="col-md-5"></div>
                    <div class="col-md-2">密&nbsp&nbsp码</div>
                    <div class="col-md-5"></div>
                </div>
                <div class="row">
                    <div class="col-md-5"></div>
                    <div class="col-md-2">
                        <input class="form-control" type="password" name="password" id="password" required>
                    </div>
                    <div class="col-md-5">
                        <button id="btn1" type="button" class="btn btn-success" onclick="show()">显示密码</button>
                    </div>
                </div>
                <div class="row" style="font-size: 110%">
                    <div class="col-md-5"></div>
                    <div class="col-md-6">已有帐号？赶快<a href="login.php">登录</a></div>
                    <div class="col-md-1"></div>
                </div>
                <?php 
                if(isset($_POST['action'])) {
                    if($_POST['action'] === 'SIGNUP') {
                        foreach($userinfo as $list) {
                            if($_POST['username'] === $list['username']) {
                                $judge = 1;
                                break;
                            }
                            else {
                                $judge = 0;
                            }
                        }
                        if($judge === 1) {?>
                        <div class="row">
                            <div class="col-md-5"></div>
                            <div class="col-md-7" style="color: #FF0000">该用户名已被注册</div>
                        </div>
                  <?php }
                        else {
                            $sql = $pdo->prepare('INSERT INTO userinfo (username, password, create_time)
                            VALUES (:username, :password, :created_time);');
                            $sql->bindValue(':username', urlencode($_POST['username']));
                            $sql->bindValue(':password', $_POST['password']);
                            $sql->bindValue(':created_time', date('Y-m-d H:i:s',time()));
                            $sql->execute();
                            header('Location:login.php');
                        }
                    }
                }
                ?>
                <br>
                <div align="center">
                    <button class="btn btn-success" type="submit" name="action" value="SIGNUP">&nbsp&nbsp注&nbsp&nbsp册&nbsp&nbsp</button>
                </div>
            </div>
        </form>
    </body>
</html>