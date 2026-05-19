<?php

require_once __DIR__ . '/providers/InfobipProvider.php';

class SmsService
{
    public static function send($phone, $message)
    {
        return InfobipProvider::send($phone, $message);
    }

}
