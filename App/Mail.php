<?php
namespace App;
//require 'vendor/autoload.php';
use Mailgun\Mailgun;


/**
 * Mail
 * PHP version 7.4.2
 */
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

        $mg = Mailgun::create(Config::MAILGUN_API_KEY);   
        // Now, compose and send your message.
        $mg->messages()->send(Config::MAILGUN_DOMAIN, array(
          'from'    => '<mailgun@sandbox30f857b1a4b74737bfe90d9cbb117047.mailgun.org>',
          'to'      => $to,
          'subject' => $subject,
          'text'    => $text,
          'html'    => $html
         ));
    }
}
?>
