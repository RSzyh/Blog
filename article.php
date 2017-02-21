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
    if($_POST['action'] === 'OUT') {
        unset($_SESSION['username']);
    }
}

$sql = $pdo->prepare('SELECT * FROM article');
$sql->execute();
$article = $sql->fetchall(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>文章</title>
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
    </head>
    <body style="background-color: #66ccff">
        <form action="article.php" method="POST">
        <div class="container-fluid">
            <br>
            <div class="row">
                <div class="col-md-9"></div>
                <?php
                if(isset($_SESSION['username'])) {?>
                <div class="col-md-1" align="center">
                    <button type="submit" class="btn btn-success" name="action" value="OUT">&nbsp&nbsp注&nbsp&nbsp销&nbsp&nbsp</button>
                </div>
                <?php
                }
                else {?>
                <div class="col-md-1" align="center">
                    <a href="login.php" class="btn btn-success">&nbsp&nbsp登&nbsp&nbsp录&nbsp&nbsp</a>
                </div>
                <?php
                }?>
                <div class="col-md-1" align="center">
                    <a href="create.php" class="btn btn-danger">书写心情</a>
                </div>
                <div class="col-md-1" align="center">
                    <a href="index.php" class="btn btn-success">返回首页</a> 
                </div>
            </div>
            <hr>
            <?php 
            if(empty($article[0])) {?>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-4" style="font-size: 130%">还没有文章哦～<a href="create.php">书写心情</a></div>
                <div class="col-md-7"></div>
            </div>
            <?php
            }
            else{
            ?>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-4">文章标题</div>
                <div class="col-md-3">作者</div>
                <div class="col-md-2">创建时间</div>
                <div class="col-md-2"></div>
            </div>
            <br>
            <?php foreach($article as $list) { ?>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-4"><a href="detail.php?id=<?php echo $list['id'];?>"><?php echo urldecode($list['title']);?></a></div>
                <div class="col-md-3"><?php echo $list['username'];?></div>
                <div class="col-md-2"><?php echo $list['create_time'];?></div>
                <div class="col-md-2"></div>
            </div>
        </div>
        <?php } 
            }?>
        </form>
    </body>
</html>