<?php
   //DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>m5-01</title>
    </head>
    <body>
        <?php
        //編集番号が投稿された場合に一致した行の各項目の値を取得
        session_start();
        if(!empty($_POST["edit"]) && !empty($_POST["password_edit"])){
            $_SESSION["edit"] = $_POST["edit"]; //値を取得したままにする？
            $sql = 'SELECT * FROM tb1';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                if($row['password_post'] == $_POST["password_edit"] && $row['id'] == $_POST["edit"]){
                    $newname = $row['name'];
                    $newcomment = $row['comment'];
                    $newpassword = $row['password_post'];
                } //ここでechoを使うとフォームの上に表示されてしまう→下に表示する方法は？
            }
        }
        ?>
        <!-- 新規登録の場合と編集の場合でフォームを差し替える -->
        <?php if(empty($_POST["edit"]))://新規登録?>
        <form action="" method="post">
            <input type="text" name="name" placeholder="名前" >
            <input type="text" name="comment" placeholder="コメント">
            <br>
            <input type="text" name="password_post" placeholder="パスワード">
            <input type="submit" name="submit" value="送信">
            <br>
        </form>
        <?php endif; if(!empty($_POST["edit"]))://編集?>
        <form action="" method="post">
            <input type="text" name="name_2" placeholder="名前" 
            value="<?php if(!empty($newname)){
                            echo $newname;
                         }
                    ?>">
            <input type="text" name="comment_2" placeholder="コメント"
            value="<?php if(!empty($newcomment)){
                            echo $newcomment;
                         }
                    ?>">
            <br>
            <input type="text" name="password_post2" placeholder="パスワード"
            value="<?php if(!empty($newpassword)){
                            echo $newpassword;
                         }
                    ?>">
            <input type="submit" name="submit" value="送信">
            <br>
        </form>
        <?php endif;?>
        <form action="" method="post">
            <br>
            <input type="text" name="delete" placeholder="削除対象番号">
            <br>
            <input type="text" name="password_delete" placeholder="パスワード">
            <input type="submit" value="削除">
        </form>
        <form action="" method="post">
            <br>
            <input type="text" name="edit" placeholder="編集番号">
            <br>
            <input type="text" name="password_edit" placeholder="パスワード">
            <input type="submit" value="編集">
        </form>
        <?php
        //新規投稿
       if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password_post"]) 
        && $_POST["name"]!="" && $_POST["comment"]!="" && $_POST["password_post"]!=""){
        //テーブル作成
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $sql = "CREATE TABLE IF NOT EXISTS tb1"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY," //各項目を設置
        . "name char(32),"
        . "comment TEXT,"
        . "date DATETIME,"
        . "password_post char(32)"
        .");";
        $stmt = $pdo->query($sql);
        //テーブルにデータを入力
        $sql = $pdo -> prepare("INSERT INTO tb1 (name, comment, date, password_post) VALUES (:name, :comment, :date, :password_post)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':password_post', $password_post, PDO::PARAM_STR);
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $date = new DATETIME();
        $date = $date -> format("Y/m/d H:i:s");
        $password_post = $_POST["password_post"];
        $sql -> execute();
        //データを表示
        $sql = 'SELECT * FROM tb1';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['date'].',';
            echo '<br>';
            echo '<hr>';
        }
        }
        
        
        //削除機能
        if(!empty($_POST["delete"]) && !empty($_POST["password_delete"])){
            $sql = 'SELECT * FROM tb1';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                if($_POST["password_delete"] == $row['password_post']){
                    $id = $_POST["delete"]; //投稿される削除番号
                    $sql = 'delete from tb1 where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                }
                //表示
                    echo $row['id'].',';
                    echo $row['name'].',';
                    echo $row['comment'].',';
                    echo $row['date'].',';
                    echo '<br>';
                    echo '<hr>';
            }
        }
            
        //編集機能
        if(!empty($_POST["name_2"]) && !empty($_POST["comment_2"]) && !empty($_POST["password_post2"])){
            $sql = 'SELECT * FROM tb1';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                $id = $_SESSION["edit"]; //変更する投稿番号
                $sql = 'UPDATE tb1 SET name=:name,comment=:comment,password_post=:password_post WHERE id=:id'; //WHERE句で指定したidを持つデータがSET句で指定した値に更新
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name',$_POST["name_2"], PDO::PARAM_STR); //新しい名前、コメント、パスワードを入れる
                $stmt->bindParam(':comment',$_POST["comment_2"], PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':password_post',$_POST["password_post2"], PDO::PARAM_INT);
                $stmt->execute(); //実行する
            }
                //表示
                $sql = 'SELECT * FROM tb1';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    echo $row['id'].',';
                    echo $row['name'].',';
                    echo $row['comment'].',';
                    echo $row['date'].',';
                    echo '<br>';
                    echo '<hr>';
                }
        }   
        
        ?>
    </body>
</html>