<?php
namespace App;

/** Flah notification messages: messages for one time display using the session
 * for storage between requests.
 * PHP version 7.4.2
 */
 class Flash{

     /**
      * Message types
      * @var string
      */
      const SUCCESS = 'success';
      const INFO = 'info';
      const WARNING = 'warning';

    /**
     * Add a message
     * @param string $message The message content
     * @param string $type The message type
     * @return void
     */
    public static function addMessage($message, $type = 'success'){
        //Create array in the session if it doesn't already exist
        if (! isset($_SESSION['flash_notifications'])){
            $_SESSION['flash_notifications'] = [];
        }
        //Append the message to the array
        $_SESSION['flash_notifications'][] = [
            'body' => $message,
            'type'=> $type];
    }
    public static function getMessages(){
        if(isset($_SESSION['flash_notifications'])){
            $messages = $_SESSION['flash_notifications'];
            unset($_SESSION['flash_notifications']);
            return $messages;
        }
    }
 }
?>