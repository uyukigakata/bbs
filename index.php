<?php 

date_default_timezone_set("Asia/Tokyo");

$comment_array = array();
$pdo = null;
$stmt = null;

// フォームが送信された時    
if (!empty($_POST["submitButton"])) {

    // 名前とコメントが空でないかチェック
    if (empty($_POST["username"]) || empty($_POST["comment"])) {
        echo "名前とコメントを入力してください";
    } else {
        // DB接続
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=bbs-yu', "root", "");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $postData = date("Y-m-d H:i:s");
        try {
            $stmt = $pdo->prepare("INSERT INTO `bbs-table` (`username`, `comment`, `postData`) VALUES (:username, :comment, :postData);");
            $stmt->bindParam(':username', $_POST['username'], PDO::PARAM_STR);
            $stmt->bindParam(':comment', $_POST['comment'], PDO::PARAM_STR);
            $stmt->bindParam(':postData', $postData, PDO::PARAM_STR);

            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        // DBの接続を閉じる
        $pdo = null;

        // リダイレクト
        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
    }
}

// DB接続
try {
    $pdo = new PDO('mysql:host=localhost;dbname=bbs-yu', "root", "");
} catch (PDOException $e) {
    echo $e->getMessage();
}

// DBからコメントデータを取得する
$sql = "SELECT `id`, `username`, `comment`, `postData` FROM `bbs-table`;";
$comment_array = $pdo->query($sql);

// DBの接続を閉じる
$pdo = null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP掲示板</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .commentSection {
            border: 1px solid #000;
            margin-bottom: 20px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1 class="title">PHP掲示板アプリケーション</h1>
    <hr>
    <div class="boardWrapper">
        <section>
            <?php foreach ($comment_array as $comment): ?>
                <article class="commentSection">
                    <div class="wrapper">
                        <div class="nameArea">
                            <span>名前:</span>
                            <p class="username"><?php echo $comment["username"]; ?></p>
                            <time>:<?php echo $comment["postData"]; ?></time>
                        </div>
                        <span>コメント：</span>
                        <p class="comment"><?php echo $comment["comment"]; ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
        <form class="formWrapper" method="post">
            <div>
                <input type="submit" value="書く" name="submitButton">
                <label for="">名前</label>
                <input type="text" name="username">
            </div>
            <div>
                <label for="">コメント</label><br />
                <textarea class="commentTextArea" name="comment"></textarea>
            </div>
        </form> 
    </div>
</body>
</html>
