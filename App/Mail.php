<?php
namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail{
    /**
     * Send message
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text only content of the message
     * @param string $html content of the message
     * @return mixed
     */
    public static function send($to, $subject, $text, $html){
        /* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
        $mail = new PHPMailer(TRUE);

        /* Open the try/catch block. */
        try {
        $mail->CharSet = "UTF-8";

        $mail->setFrom('appSBMailer@gmail.com', 'Coiny - Sylwia Brant');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->isHTML(TRUE);
        $mail->Body = $html;
        $mail->AltBody = $text;

        /* SMTP parameters. */
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Username = 'appSBMailer@gmail.com';
        $mail->Password = '(Na1G2eL)';
        
        $mail->send();
        }
        catch (Exception $e)
        {
            echo $e->errorMessage();
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
        }
    }
}

?>
