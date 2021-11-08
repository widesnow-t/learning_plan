<?php
require_once __DIR__ . '/functions.php';

//index.phpから渡されたidを受け取る
$id = filter_input(INPUT_GET, 'id');
//受けとったidのレコードを収録
$plan = findById($id);
//初期化
$title = '';
$due_date = '';
// エラーチェック用の配列
$errors = [];
$errors_required = [];
$errors_same = [];

// リクエストメソッドの判定
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームに入力されたデータの受け取り
    $title = filter_input(INPUT_POST, 'title');
    $due_date = filter_input(INPUT_POST, 'due_date');

    // バリデーション
    $errors_required = validateRequired($title, $due_date);

    // 学習内容に入力がある場合は、同じ学習内容のデータが存在しないかチェック
    if ($title) {
        $errors_same = validateSameMeasDate($title);
    }

    // エラーメッセージの配列をマージ
    $errors = array_merge($errors_required, $errors_same);

    if (empty($errors)) {
        updatePlan($id, $title, $due_date);

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<!-- _head.phpの読み込み -->
<?php include_once __DIR__ . '/_head.html' ?>

<body>
    <div class="wrapper">
        <h1 class="title">学習管理アプリ</h1>
        <div class="form-area">
            <!-- エラー表示 -->
            <?php if ($errors) echo (createErrMsg($errors)) ?>
            <form action="" method="post">
                <label for="title">学習内容</label>
                <input type="text" id="title" name="title" value="<?= h($plan['title']) ?>">
                <label for="due_date">期限日</label>
                <input type="date" id="due_date" name="due_date" value="<?= h($plan['due_date']) ?>">
                <input type="submit" class="btn submit-btn" value="更新">
            </form>
            <a href="index.php" class="btn addition-btn">戻る</a>
        </div>
    </div>
</body>