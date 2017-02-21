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

$sql = $pdo->prepare('SELECT * FROM article WHERE id = :id;');
$sql->bindValue(':id', $_GET['id']);
$sql->execute();
$article = $sql->fetch(PDO::FETCH_ASSOC);
if($article === false) {
    echo '<h1>404</h1>';
    return;
}

session_start();

if(isset($_POST['action'])) {
    if(isset($_SESSION['username'])) {
        if($_POST['action'] === 'SUB1' && $_POST['comment'] <> "") {
            $sql = $pdo->prepare('INSERT INTO comment (A_id, username, create_time, content)
            VALUES (:A_id, :username, :create_time, :content);');
            $sql->bindValue(':A_id', $_GET['id']);
            $sql->bindValue(':username', $_SESSION['username']);
            $sql->bindValue(':create_time', date('Y-m-d H:i:s',time()));
            $sql->bindValue(':content', urlencode($_POST['comment']));
            $sql->execute();
        }
        else if(substr($_POST['action'],0,4) === 'SUB2') {
            $id = substr($_POST['action'],5);
            $sql = $pdo->prepare('INSERT INTO comment (A_id, pid, username, create_time, content)
            VALUES (:A_id, :pid, :username, :create_time, :content);');
            $sql->bindValue(':A_id', $_GET['id']);
            $sql->bindValue(':pid', $id);
            $sql->bindValue(':username', $_SESSION['username']);
            $sql->bindValue(':create_time', date('Y-m-d H:i:s',time()));
            $sql->bindValue(':content', urlencode($_POST['reply-'.$id]));
            $sql->execute();
        }
        else if($_POST['action'] === 'OUT') {
            unset($_SESSION['username']);
        }
        else if($_POST['action'] === 'DEL') {
            $sql = $pdo->prepare('DELETE FROM article WHERE id = :id');
            $sql->bindValue(':id', $_GET['id']);
            $sql->execute();
            header('Location: article.php');
        }
    }
    else {
        header('Location:login.php');
    }
}

$sql = $pdo->prepare('SELECT * FROM comment WHERE A_id = :A_id;');
$sql->bindValue(':A_id', $_GET['id']);
$sql->execute();
$comment = $sql->fetchall(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>详情</title>
        <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script>
        function show(x) {
            if(document.getElementById(x).style.display === "none") {
                document.getElementById(x).style.display="block";
                document.getElementById('btn'+x).innerHTML="关闭";
            }
            else {
                document.getElementById(x).style.display="none";
                document.getElementById('btn'+x).innerHTML="回复";
            }
        }
        </script>
    </head>
    <body style="background-color: #66ccff">
        <form action="detail.php?id=<?php echo $article['id']?>" method="POST">
        <div class="container-fluid">
            <br>
            <div class="row">
                <div class="col-md-8"></div>
                <?php
                if(isset($_SESSION['username'])) {
                    if($_SESSION['username'] === $article['username']) {
                ?>
                <div class="col-md-1" align="center">
                    <a href="edit.php?id=<?php echo $_GET['id'];?>" class="btn btn-danger">修改文章</a>
                </div>
                <?php
                    }
                    else if($_SESSION['username'] === 'admin') {
                ?>
                <div class="col-md-1" align="center">
                    <button type="submit" class="btn btn-success" name="action" value="DEL">删除文章</button>
                </div>
                    <?php }
                    else {?>
                <div class="col-md-1" align="center"></div>
                <?php
                    }?>
                <div class="col-md-1" align="center">
                    <button type="submit" class="btn btn-success" name="action" value="OUT">&nbsp&nbsp注&nbsp&nbsp销&nbsp&nbsp</button>
                </div>
                <?php
                }
                else {?>
                <div class="col-md-1" align="center"></div>
                <div class="col-md-1" align="center">
                    <a href="login.php" class="btn btn-success">&nbsp&nbsp登&nbsp&nbsp录&nbsp&nbsp</a>
                </div>
                <?php
                }?>
                <div class="col-md-1" align="center">
                    <a href="article.php" class="btn btn-danger">文章列表</a>
                </div>
                <div class="col-md-1" align="center">
                    <a href="index.php" class="btn btn-danger">返回首页</a>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-1"><?php echo $article['username']?></div>
                <div class="col-md-10" style="font-size: 150%"><?php echo $article['title']?></div>
                <div class="col-md-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <pre style="width: 100%; height: 500px;font-size: 120%"><?php echo $article['content'];?></pre>
                </div>
                <div class="col-md-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10" style="font-size: 150%">留言区</div>
                <div class="col-md-1"></div>
            </div>
            <?php
            if(isset($_SESSION['username'])) {?>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <textarea placeholder="在此留言" class="form-control" style="resize: none;height: 300px;font-size: 120%" name="comment"></textarea>
                </div>
                <div class="col-md-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10" align="right">
                    <button class="btn btn-danger" type="submit" name="action" value="SUB1">提交</button>
                </div>
                <div class="col-md-1"></div>
            </div>
            <br>

            <?php 
            foreach($comment as $comment1) {
                if($comment1['pid'] === NULL) {?>
            <div class="row">
                <div class="col-md-1"><?php echo $comment1['username'];?></div>
                <div class="col-md-10"><?php echo urldecode($comment1['content']);?></div>
                <div class="col-md-1">
                    <button id="btn<?php echo $comment1['id'];?>" class="btn btn-danger" type="button" onclick="show(<?php echo "'".$comment1['id']."'"?>)">回复</button>
                </div>
            </div>
            <div class="row" style="display: none" id="<?php echo $comment1['id'];?>">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <textarea placeholder="在此回复" class="form-control" style="resize: none;height: 200px;font-size: 120%" name="reply-<?php echo $comment1['id']?>"></textarea>
                </div>
                <div class="col-md-1">
                    <button type="submit" name="action" value="SUB2-<?php echo $comment1['id'];?>">提交</button>
                </div>
            </div>
            <?php
                }?>
            
            <?php 
            foreach($comment as $comment2) {
                if($comment2['pid'] === $comment1['id']) {?>

            <div class="row">
                <div class="col-md-2" align="right"><?php echo $comment2['username'];?>&nbsp回复&nbsp<?php echo $comment1['username'];?></div>
                <div class="col-md-9"><?php echo urldecode($comment2['content']);?></div>
                <div class="col-md-1">
                    <button id="btn<?php echo $comment2['id'];?>" class="btn btn-danger" type="button" onclick="show(<?php echo "'".$comment2['id']."'"?>)">回复</button>
                </div>
            </div>
            <div class="row" style="display: none" id="<?php echo $comment2['id'];?>">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <textarea placeholder="在此回复" class="form-control" style="resize: none;height: 200px;font-size: 120%" name="reply-<?php echo $comment2['id']?>"></textarea>
                </div>
                <div class="col-md-1">
                    <button type="submit" name="action" value="SUB2-<?php echo $comment2['id'];?>">提交</button>
                </div>
            </div>

                <?php }
            }
            }
            }
            else {?>
            <div align="center" style="font-size: 130%"><a href="login.php">登录</a>才可以留言哦～</div>
            <br>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <textarea placeholder="在此留言" class="form-control" style="resize: none;height: 300px;font-size: 120%" name="comment" disabled></textarea>
                </div>
                <div class="col-md-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10" align="right">
                    <button class="btn btn-danger" type="submit" name="action" value="SUB1" disabled>提交</button>
                </div>
                <div class="col-md-1"></div>
            </div>
            <br>

            <?php 
            foreach($comment as $comment1) {
                if($comment1['pid'] === NULL) {?>
            <div class="row">
                <div class="col-md-1"><?php echo $comment1['username'];?></div>
                <div class="col-md-10"><?php echo urldecode($comment1['content']);?></div>
                <div class="col-md-1">
                    <button id="btn<?php echo $comment1['id'];?>" class="btn btn-danger" type="button" onclick="show(<?php echo "'".$comment1['id']."'"?>)" disabled>回复</button>
                </div>
            </div>
            <div class="row" style="display: none" id="<?php echo $comment1['id'];?>">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <textarea placeholder="在此回复" class="form-control" style="resize: none;height: 200px;font-size: 120%" name="reply-<?php echo $comment1['id']?>"></textarea>
                </div>
                <div class="col-md-1">
                    <button type="submit" name="action" value="SUB2-<?php echo $comment1['id'];?>">提交</button>
                </div>
            </div>
            <?php
                }?>
            
            <?php 
            foreach($comment as $comment2) {
                if($comment2['pid'] === $comment1['id']) {?>

            <div class="row">
                <div class="col-md-2" align="right"><?php echo $comment2['username'];?>&nbsp回复&nbsp<?php echo $comment1['username'];?></div>
                <div class="col-md-9"><?php echo urldecode($comment2['content']);?></div>
                <div class="col-md-1">
                    <button id="btn<?php echo $comment2['id'];?>" class="btn btn-danger" type="button" onclick="show(<?php echo "'".$comment2['id']."'"?>)" disabled>回复</button>
                </div>
            </div>
            <div class="row" style="display: none" id="<?php echo $comment2['id'];?>">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <textarea placeholder="在此回复" class="form-control" style="resize: none;height: 200px;font-size: 120%" name="reply-<?php echo $comment2['id']?>"></textarea>
                </div>
                <div class="col-md-1">
                    <button type="submit" name="action" value="SUB2-<?php echo $comment2['id'];?>">提交</button>
                </div>
            </div>
        <?php
                }
            }
            }
        }?>
            <hr>
            <div align="center">Copyright &copy; 2017 By RSzyh</div>
        </div>
        </form>
    </body>
</html>