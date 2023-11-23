<?php
require_once("./FormPreprocessor.php");

use App\Form\FormPreprocessor;

$form = new FormPreprocessor();
if (! empty($_POST)) {
    $form->formInput($_POST["inputArea"]);
}
// $form->formInput($_POST["inputArea"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<form class="form" method="post" enctype="multipart/form-data">
    <textarea name="inputArea"></textarea>
    <label for="servers">Сервер</label>
    <select id="servers" name="servers">
        <option value="s1">Сервер1</option>
        <option value="s2">Сервер2</option>
        <option value="s3">Сервер3</option>
        <option value="s4">Сервер4</option>
    </select>
    <button type="submit">Запуск обновления</button>
</form>
</body>
</html>