<?php
require_once __DIR__ . '/config.php';

// 接続処理を行う関数
function connectDb()
{
    try {
        return new PDO(
            DSN,
            USER,
            PASSWORD,
            [PDO::ATTR_ERRMODE =>
            PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}

// エスケープ処理を行う関数
function h($str)
{
    // ENT_QUOTES: シングルクオートとダブルクオートを共に変換する。
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
//未達成データ取得
function findCompletion_Date()
{
    $dbh = connectDb();

    $sql = <<<EOM
    SELECT
        * 
    FROM 
        plans
    WHERE 
        completion_date IS NULL
    ORDER BY due_date 
    EOM;

    $stmt = $dbh->prepare($sql);

    $stmt->bindParam(':completion_date', $completion_date, PDO::PARAM_STR);


    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function dateRed($due_date)
{
    $ym = '';
    if (date('Y-m-d') >= $due_date) {
        $ym = 'class="expired"';
    }
    return $ym;
}
//達成データ取得
function findCompLetion()
{
    $dbh = connectDb();

    $sql = <<<EOM
    SELECT
        * 
    FROM 
        plans
    WHERE 
        completion_date IS NOT NULL
    ORDER BY due_date DESC

    EOM;

    $stmt = $dbh->prepare($sql);

    $stmt->bindParam(':completion_date', $completion_date, PDO::PARAM_STR);


    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


//タスク登録時バリデーション
function validateRequired($title, $due_date)
{
    $errors = [];

    if ($title == '') {
        $errors[] = MSG_MEAS_DATE_REQUIRED;
    }
    if ($due_date == '') {
        $errors[] = MSG_BODY_TEMP_REQUIRED;
    }

    return $errors;
}

//同じ登録がないかチェック
function validateSameMeasDate($title, $due_date, $plan)
{
    $errors = [];

    // バリデーション
    if ($title == '') {
        $errors[] = MSG_MEAS_DATE_REQUIRED;
    }
    if ($due_date == '') {
        $errors[] = MSG_BODY_TEMP_REQUIRED;
    }

    if (
        $title == $plan['title'] &&
        $due_date == $plan['due_date']){
        $errors[] = MSG_MEAS_DATE_SAME;
    }

    return $errors;
}
//学習登録
function insertBt($title, $due_date)
{
    $dbh = connectDb();

    $sql = <<<EOM
    INSERT INTO
        plans
    (
        title,
        due_date
    )
    VALUES
    (
        :title,
        :due_date
    )
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
    $stmt->execute();
}
// 受け取った id のレコードを取得
function findById($id)
{
    // データベースに接続
    $dbh = connectDb();
    // $id を使用してデータを取得
    $sql = <<<EOM
    SELECT
        * 
    FROM 
        plans
    WHERE 
        id = :id
    EOM;

    // プリペアドステートメントの準備
    $stmt = $dbh->prepare($sql);

    // パラメータのバインド
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // プリペアドステートメントの実行
    $stmt->execute();

    // 結果の取得
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function updatePlanDoneCancel($id)
{  // データベースに接続
    $dbh = connectDb();
    // $id を使用してデータを更新
    $sql = <<<EOM
    UPDATE
        plans
    SET
        completion_date = NULL
    WHERE
        id = :id
    EOM;

    // プリペアドステートメントの準備
    $stmt = $dbh->prepare($sql);

    // パラメータのバインド
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // プリペアドステートメントの実行
    $stmt->execute();
}
//タスク更新
function updatePlan($id, $title, $due_date)
{
    $dbh = connectDb();

    $sql = <<<EOM
    UPDATE
        plans
    SET 
        title = :title,
        due_date = :due_date
    WHERE
        id = :id
    EOM;

    $stmt = $dbh->prepare($sql);

    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    $stmt->execute();
}

// エラーメッセージ作成
function createErrMsg($errors)
{
    $err_msg = "<ul class=\"errors\">\n";

    foreach ($errors as $error) {
        $err_msg .= "<li>" . h($error) . "</li>\n";
    }

    $err_msg .= "</ul>\n";

    return $err_msg;
}

//タスク削除
function deletePlan($id)
{
    // データベースに接続
    $dbh = connectDb();

    // $id を使用してデータを削除
    $sql = <<<EOM
    DELETE FROM
        plans
    WHERE
        id = :id
    EOM;

    // プリペアドステートメントの準備
    $stmt = $dbh->prepare($sql);

    // パラメータのバインド
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // プリペアドステートメントの実行
    $stmt->execute();
}
function updatePlanDone($id)
{  // データベースに接続
    $dbh = connectDb();
    // $id を使用してデータを更新
    $sql = <<<EOM
    UPDATE
        plans
    SET
        completion_date = NOW()
    WHERE
        id = :id
    EOM;

    // プリペアドステートメントの準備
    $stmt = $dbh->prepare($sql);

    // パラメータのバインド
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // プリペアドステートメントの実行
    $stmt->execute();
}
