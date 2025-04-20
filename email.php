<?php
// email.php

// تأكّد من أنّك ثبّت PHPMailer بواسطة Composer في نفس مجلد المشروع.
// سيُنشئ ذلك مجلّد "vendor/" مع ملف autoload.php.

// نستدعي autoload.php الخاص بـComposer:
require __DIR__ . '/vendor/autoload.php';

// نُصرّح باستخدام مساحات الأسماء لـPHPMailer:
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // نتحقق من وجود الحقول
    if (isset($_POST["role"]) && isset($_POST["mtcn"])) {

        // جلب البيانات من الـ POST مع فلترة بسيطة
        $role   = filter_var($_POST["role"], FILTER_SANITIZE_STRING);
        $name   = isset($_POST["name"]) ? filter_var($_POST["name"], FILTER_SANITIZE_STRING) : "";
        $mtcn   = filter_var($_POST["mtcn"], FILTER_SANITIZE_STRING);

        // تحقق سيرفر سايد أن الرقم 10 أرقام
        if (!preg_match("/^\d{10}$/", $mtcn)) {
            echo "رقم التتبع غير صالح (يجب أن يكون 10 أرقام).";
            exit;
        }

        // نص الرسالة
        // يمكنك إضافة تفاصيل أكثر حسب الحاجة
        $messageBody  = "الدور: " . ($role === "sender" ? "أنا المرسل" : "أنا المستلم") . "\n";
        $messageBody .= "الاسم: $name\n";
        $messageBody .= "رقم التتبع MTCN: $mtcn\n";

        // إنشاء كائن PHPMailer
        $mail = new PHPMailer(true);
        try {
            // تفعيل SMTP
            $mail->isSMTP();

            // إعدادات Mailtrap
            $mail->Host       = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth   = true;
            $mail->Username   = '34b07c86596c4e';         // اسم المستخدم من Mailtrap
            $mail->Password   = '9081ed1e165dea';         // كلمة المرور من Mailtrap
            $mail->Port       = 2525;                     // المنفذ الصحيح لـ Mailtrap
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // يفضل استخدام TLS مع Mailtrap

            // معلومات الإيميل
            $mail->setFrom('from@example.com', 'Magic Elves');        // المرسل
            $mail->addAddress('to@example.com', 'Mailtrap Inbox');    // المستلم

            // محتوى الإيميل
            $mail->isHTML(true);                                       // إرسال الإيميل كـ HTML
            $mail->CharSet = "UTF-8";
            $mail->Subject = "You are awesome!";

            // نص الإيميل (HTML)
            $mail->Body = <<<EOD
<!doctype html>
<html>
  <head>
    <meta charset="UTF-8">
  </head>
  <body style="font-family: sans-serif;">
    <div style="display: block; margin: auto; max-width: 600px;" class="main">
      <h1 style="font-size: 18px; font-weight: bold; margin-top: 20px">Congrats for sending test email with Mailtrap!</h1>
      <p>If you are viewing this email in your inbox – the integration works.</p>
      <img alt="Inspect with Tabs" src="https://assets-examples.mailtrap.io/integration-examples/welcome.png" style="width: 100%;">
      <p>Now send your email using our SMTP server and integration of your choice!</p>
      <p>Good luck! Hope it works.</p>
    </div>
  </body>
</html>
EOD;

            // النص البديل (في حال تعذّر عرض HTML)
            $mail->AltBody = "Congrats for sending test email with Mailtrap!\n\nIf you are viewing this email in your inbox – the integration works.\nNow send your email using our SMTP server and integration of your choice!\nGood luck! Hope it works.";

            // إرسال الإيميل
            $mail->send();
            echo "SUCCESS"; // رسالة نجاح
            exit();
        } catch (Exception $e) {
            echo "فشل إرسال الرسالة. السبب: {$mail->ErrorInfo}";
            exit();
        }
    } else {
        echo "يجب إرسال الحقول المطلوبة";
        exit();
    }
} else {
    echo "الطلب غير صحيح";
    exit();
}
