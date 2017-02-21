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

session_start();
if(isset($_POST['action'])) {
    if(isset($_SESSION['username'])) {
    if($_POST['action'] === 'SAVE') {
        $sql = $pdo->prepare('INSERT INTO article (username, title, content, create_time)
        VALUES (:username, :title, :content, :create_time)');
        $sql->bindValue(':username', $_SESSION['username']);
        $sql->bindValue(':title', urlencode($_POST['title']));
        $sql->bindValue(':content', urlencode($_POST['content']));
        $sql->bindValue(':create_time', date('Y-m-d H:i:s',time()));
        $sql->execute();
        header('Location: article.php');
    }
    }
    else {
        header('Location: login.php');
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>创建文章</title>
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
    </head>
    <body style="background-color:#66ccff">
        <form action="create.php" method="post">
        <?php
        if(isset($_SESSION['username'])) {?>
        <div class="container-fluid">
            <br>
            <div class="row">
                <div class="col-md-10"></div>
                <div class="col-md-1">
                    <a href="article.php" class="btn btn-danger">文章列表</a>
                </div>
                <div class="col-md-1">
                    <a href="index.php" class="btn btn-success">返回首页</a> 
                </div>
            </div>
            <hr>
            <div class="row" style="margin: 20px">
                <div align="right" style="font-size:130%" class="col-md-1 col-xs-1">标题：</div>
                <div class="col-md-6 col-xs-6">
                    <input type="text" style="font-size:120%" placeholder="在此输入标题" class="form-control" name="title" required>
                </div>
                <div class="col-md-5 col-xs-5"></div>
            </div>
            <div class="row" style="margin: 20px">
                <div align="right" style="font-size:130%;margin-top:4px" class="col-md-1 col-xs-1">内容：</div>
                <div class="col-md-6 col-xs-6">
                    <textarea placeholder="在此输入内容" style="font-size:120%;resize: none;margin: 0px;width: 170%;height: 300px" class="form-control" name="content" required></textarea>
                </div>
                <div class="col-md-5 col-xs-5"></div>
            </div>
        </div>
        <div align="center" style="margin: 10px">
            <button type="submit" class="btn btn-success" name="action" value="SAVE">&nbsp保&nbsp存&nbsp</button>
        </div>
        <?php
        }
        else {?>
        <div class="container-fluid">
            <br>
            <div class="row">
                <div class="col-md-10"></div>
                <div class="col-md-1">
                    <a href="article.php" class="btn btn-danger">文章列表</a>
                </div>
                <div class="col-md-1">
                    <a href="index.php" class="btn btn-success">返回首页</a> 
                </div>
            </div>
            <hr>
            <div align="center" style="font-size: 120%;color: #FF0000">您还未登录，请先<a href="login.php">登录</a></div>
            <div class="row" style="margin: 20px">
                <div align="right" style="font-size:130%" class="col-md-1 col-xs-1">标题：</div>
                <div class="col-md-6 col-xs-6">
                    <input type="text" style="font-size:120%" placeholder="在此输入标题" class="form-control" name="title" disabled>
                </div>
                <div class="col-md-5 col-xs-5"></div>
            </div>
            <div class="row" style="margin: 20px">
                <div align="right" style="font-size:130%;margin-top:4px" class="col-md-1 col-xs-1">内容：</div>
                <div class="col-md-6 col-xs-6">
                    <textarea placeholder="在此输入内容" style="font-size:120%;resize: none;margin: 0px;width: 170%;height: 300px" class="form-control" name="content" disabled></textarea>
                </div>
                <div class="col-md-5 col-xs-5"></div>
            </div>
        </div>
        <div align="center" style="margin: 10px">
            <button type="submit" class="btn btn-success" name="action" value="SAVE" disabled>&nbsp保&nbsp存&nbsp</button>
        </div>
        <?php
        }
        ?>
        <hr>
        <div align="center">Copyright &copy; 2017 By RSzyh</div>
        </form>
    </body>
</html>