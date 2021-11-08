<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/config.php';
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
        insertBt($title, $due_date);
    }
}
$comps = findCompletion_Date(TASK_COMPLETION_DEU_DATE);
$letions = findCompLetion(TASK_COMPLETION_DATE_ISNOTNULL);
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
                <input type="text" name="title" value="<?= h($title) ?>">
                <label for="due_date">期限日</label>
                <input type="date" name="due_date" value="<?= h($due_date) ?>">
                <input type="submit" class="btn submit-btn" value="追加">
            </form>
        </div>
        <div class="incomplete-area">
            <h2 class="sub-title">未達成</h2>
            <hr>
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
                            <th><?= h($comp['title']) ?></th>
                            <?php $ym = date('Y-m-d');
                            if ($ym >= ($comp['due_date'])) : ?>
                                <th class="expired"><?= h(date('Y/m/d', strtotime($comp['due_date']))) ?></th>
                            <?php else : ?>
                                <th><?= h(date('Y/m/d', strtotime($comp['due_date']))) ?></th>
                            <?php endif ?>
                            <th><a href="done.php?id=<?= h($comp['id']) ?>" class="btn done-btn">完了</a></th>
                            <th><a href="edit.php?id=<?= h($comp['id']) ?>" class="btn edit-btn">編集</a></th>
                            <th><a href="delete.php?id=<?= h($comp['id']) ?>" class="btn delete-btn">削除</a></th>
                        <?php endforeach; ?>
                        <!-- 未完了のデータを表示 -->

                </tbody>
            </table>
        </div>
        <div class="complete-area">
            <h2 class="sub-title">完了</h2>
            <hr>
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
                            <th><?= h($letion['title']) ?></th>
                            <th><?= h(date('Y/m/d', strtotime($letion['due_date']))) ?></th>
                            <th><a href="done_cancel.php?id=<?= h($letion['id']) ?>" class="btn cancel-btn">未完了</a></th>
                            <th><a href="edit.php?id=<?= h($letion['id']) ?>" class="btn edit-btn">編集</a></th>
                            <th><a href="delete.php?id=<?= h($letion['id']) ?>" class="btn delete-btn">削除</a></th>

                        <?php endforeach; ?>


                        <!-- 完了済のデータを表示 -->

                </tbody>
            </table>
        </div>
    </div>
</body>