<?php
    $msg = null;
    $type = null;

    if ($_POST['submit']) {
        $filesDirectory = __DIR__ . '/files/';

        function mail_attachment($mailto, $from_mail, $subject, $message, $filePath = null) {
            $boundary = md5(time());

            $headers = 'From: ' . $from_mail . PHP_EOL;
            $headers .= 'Reply-To: ' . $from_mail . PHP_EOL;
            $headers .= 'MIME-Version: 1.0' . PHP_EOL;
            $headers .= 'Content-Type: multipart/mixed; boundary="' . $boundary . '"' . PHP_EOL;
            
            $body = '--' . $boundary . PHP_EOL;
            $body .= 'Content-Transfer-Encoding: 7bit' . PHP_EOL;
            $body .= 'This is a MIME encoded message.' . PHP_EOL;
            
            $body = '--' . $boundary . PHP_EOL;
            $body .= 'Content-type: text/plain; charset=utf-8' . PHP_EOL;
            $body .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
            $body .= $message . PHP_EOL;

            if (!is_null($filePath)) {
                $file_size = filesize($filePath);
                $handle = fopen($filePath, "r");
                $content = fread($handle, $file_size);	
                fclose($handle);
                $content = chunk_split(base64_encode($content));
                $name = basename($filePath);

                $body .= '--' . $boundary . PHP_EOL;
                $body .= 'Content-Type: multipart/mixed; name="' . $name . '"' . PHP_EOL;
                $body .= 'Content-Transfer-Encoding: base64' . PHP_EOL;
                $body .= 'Content-Disposition: attachment; filename="' . $name . '"' . PHP_EOL . PHP_EOL;
                $body .= $content . PHP_EOL;
                $body .= '--' . $boundary . '--';
            }

            return mail($mailto, $subject, $body, $headers);
        }

        if($_POST['subject'] == 1) {
            $subject = 'Вопрос по работе';
        } elseif ($_POST['subject'] == 2) {
            $subject = 'Вопрос о сотрудничестве';
        } elseif ($_POST['subject'] == 3) {
            $subject = 'Благодарность';
        } else {
            $subject = 'Вопрос по работе';
        }

        $to = "parfenovasasha2003@yandex.ru";
        $from = trim($_POST['email']);

        $message = htmlspecialchars($_POST['message']);
        $message = urldecode($message);
        $message = trim($message);

        $filePath = null;

        if (!empty($_FILES['upl']['name'])) {
            $name = basename($_FILES['upl']['name']);
            $filePath =  $filesDirectory . $name;
            move_uploaded_file($_FILES['upl']['tmp_name'], $filePath);
        }

        if (mail_attachment($to, $from, $subject, $message, $filePath)) {
            $type = 'alert_success';
            $msg = 'Письмо отправлено!';
        } else {
            $type = 'alert_error';
            $msg = 'Проверьте введённые данные';
        }
    }
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <title>Отправьте письмо!</title>
    </head>
    <body>
        <? if ($_POST['submit'] && !is_null($msg)) { ?>
            <div class="alert <?=$type?>"><?=$msg?></div>
        <? } ?>
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <select name="subject">
                <option disabled selected>Выберите тему письма</option>
                <option value="1">Вопрос по работе</option>
                <option value="2">Вопрос о сотрудничестве</option>
                <option value="3">Благодарность</option>
            </select>
            <input type="email" name="email" placeholder="Введите email" maxlength="30" required>
            <textarea name="message" placeholder="Введите Ваше сообщение" maxlength="300" required></textarea>
            <input name="upl" type="file">
            <input type="submit" name="submit" value="Отправить">
        </form>
    </body>
</html>