<?php
require_once __DIR__ . '/functions.php';

// エラーチェック用の配列
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームに入力されたデータの受け取り
    $title = filter_input(INPUT_POST, 'title');
    $due_date = filter_input(INPUT_POST, 'due_date');

    // バリデーション
    $errors = validateRequired($title, $due_date);
    if (empty($errors)) {
        insertBt($title, $due_date);

        header('Location: index.php');
        exit;
    }
}
$comps = findCompletion_Date();
$letions = findCompletion();
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
                <input type="text" name="title" value="">
                <label for="due_date">期限日</label>
                <input type="date" name="due_date" value="">
                <input type="submit" class="btn submit-btn" value="追加">
            </form>
        </div>
        <div class="incomplete-area">
            <h2 class="sub-title">未達成</h2>
            <table class="plan-list">
                <thead>
                    <tr>
                        <th class="plan-title">学習内容</th>
                        <th class="plan-due-date">完了期限</th>
                        <th class="done-link-area"></th>
                        <th class="edit-link-area"></th>
                        <th class="delete-link-area"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comps as $comp) : ?>
                        <tr>
                            <td><?= h($comp['title']) ?></td>
                            <td <?= dateRed($comp['due_date']) ?>><?= date('Y/m/d', strtotime(h($comp['due_date']))) ?></td>
                            <td><a href="done.php?id=<?= h($comp['id']) ?>" class="btn done-btn">完了</a></td>
                            <td><a href="edit.php?id=<?= h($comp['id']) ?>" class="btn edit-btn">編集</a></td>
                            <td><a href="delete.php?id=<?= h($comp['id']) ?>" class="btn delete-btn">削除</a></td>
                        </tr>
                    <?php endforeach; ?>

                        <!-- 未完了のデータを表示 -->

                </tbody>
            </table>
        </div>
        <div class="complete-area">
            <h2 class="sub-title">完了</h2>
            <table class="plan-list">
                <thead>
                    <tr>
                        <th class="plan-title">学習内容</th>
                        <th class="plan-completion-date">完了日</th>
                        <th class="done-link-area"></th>
                        <th class="edit-link-area"></th>
                        <th class="delete-link-area"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($letions as $letion) : ?>
                        <tr>
                            <td><?= h($letion['title']) ?></td>
                            <td><?= h(date('Y/m/d', strtotime($letion['completion_date']))) ?></td>
                            <td><a href="done_cancel.php?id=<?= h($letion['id']) ?>" class="btn cancel-btn">未完了</a></td>
                            <td><a href="edit.php?id=<?= h($letion['id']) ?>" class="btn edit-btn">編集</a></td>
                            <td><a href="delete.php?id=<?= h($letion['id']) ?>" class="btn delete-btn">削除</a></td>
                        </tr>
                    <?php endforeach; ?>
                        <!-- 完了済のデータを表示 -->
                </tbody>
            </table>
        </div>
    </div>
</body>