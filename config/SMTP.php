<?php

// Configuration for PHPMailer
return [
    'DefaultFrom' => 'mail@10kilobyte.com',
    'DefaultFromName' => 'Blood Pressure log',
    'Host' => 'smtp-relay.sendinblue.com',
    'Port' => 587,
    'SMTPAuth' => true,
    'SMTPSecure' => 'tls',
    'Username' => 'username',
    'Password' => 'password',
    'SMTPDebug' => 0
];
