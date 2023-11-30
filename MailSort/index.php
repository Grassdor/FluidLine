<?php
ini_set('memory_limit', '256M');
error_reporting(E_ALL);

require_once('./src/MailProcessor.php');

use App\MailSort\MailProcessor;

$mailProcessor = new MailProcessor();
$message = "";
$domen_arr = (int) file_get_contents("library/counter.txt");

if (php_sapi_name() === 'cli') {
    echo "Введите URL с данными для обработки: ";
    $fileUrl = trim(fgets(STDIN));
    $mailProcessor->processFile($fileUrl);
} else {
    if (!empty($_FILES)) {
        $mailProcessor->handleWebUpload($_FILES['userFile']['tmp_name']);
        $message = "Сортировка доменов прошла успешно, ваши файлы находятся в папке library.\nВ файле !unify находятся только уникальные домены";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сортировка почтовых доменов</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <p><?= $message ?></p>

    <form method="post" enctype="multipart/form-data">
        <input name="userFile" type="file"><br>
        <button type="submit">Начать обработку</button>
    </form>
    <?php

        while ($domen_arr > 0) { ?>
            <h1>Сортировка №<?= $domen_arr ?></h1>
            <div class="links">
                <?php

                $linkGenerator = new DirectoryIterator("library/process{$domen_arr}");
                foreach ($linkGenerator as $link) {
                    if ($link->getFilename() === "!unify.csv") { ?>
                        <?php $fp = "library/process{$domen_arr}/" . $link->getFilename(); ?>
                        <a style="color: red;" href="<?= $fp ?? "" ?>"><?= $link->getFilename() ?></a>
                    <?php } elseif ($link->getFilename() !== "." && $link->getFilename() !== "..") { ?>
                        <?php $fp = "library/process{$domen_arr}/" . $link->getFilename(); ?>
                        <a href="<?= $fp ?? "" ?>"><?= $link->getFilename() ?></a>
                    <?php } ?>
                <?php }
                $domen_arr--; ?>
            </div>
            <hr style="margin: 50px 0" />
        <?php } ?>
</body>

</html>