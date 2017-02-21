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

$sql = $pdo->prepare('SELECT * FROM userinfo');
$sql->execute();
$userinfo = $sql->fetchall(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>登录页面</title>
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
    </head>
    <body>
        <form action="login.php" method="POST">
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
                        <input class="form-control" type="text" name="username" required>
                    </div>
                    <div class="col-md-5"></div>
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
                        <input class="form-control" type="password" name="password" required>
                    </div>
                    <div class="col-md-5"></div>
                </div>
                <div class="row" style="font-size: 110%">
                    <div class="col-md-5"></div>
                    <div class="col-md-2">没有帐号？赶快<a href="sign_up.php">注册</a>吧～</div>
                    <div class="col-md-5"></div>
                </div>
                <div class="row">
                    <div class="col-md-5"></div>
                    <div class="col-md-2" style="color:#FF0000">
                <?php
                if(isset($_POST['action'])) {
                    if($_POST['action'] === 'LOGIN') {
                        foreach($userinfo as $list) {
                            if($list['username'] === $_POST['username'] && $list['password'] === $_POST['password']) {
                                $judge = 2;
                                break;
                            }
                            else if($list['username'] === $_POST['username'] && $list['password'] <> $_POST['password']){
                                $judge = 1;
                                break;
                            }
                            else if($list['username'] <> $_POST['username']) {
                                $judge = 0;
                            }
                        }
                        if($judge === 2) {
                            session_start();
                            $_SESSION['username'] = $_POST['username'];
                            header('Location: article.php');
                        }
                        else if($judge === 1) {
                            echo "密码输入错误！";
                        }
                        else {
                            echo "用户名不存在！";
                        }
                    }
                }
                ?>
                    </div>
                    <div class="col-md-5"></div>
                </div>
                <br>
                <div align="center">
                    <button class="btn btn-success" type="submit" name="action" value="LOGIN">&nbsp&nbsp登&nbsp&nbsp录&nbsp&nbsp</button>
                </div>
            </div>
        </form>
    </body>
</html> 