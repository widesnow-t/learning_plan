<?php
require_once __DIR__ . '/functions.php';

//index.phpから渡されたidを受け取る
$id = filter_input(INPUT_GET, 'id');

updateCompLetiondate($id);

header('Location: index.php');
exit;