<?php
return [
    'components' => [
        'db' => [
           'class' => 'yii\db\Connection',

           'username' => 'oper1',
	       'dsn' => 'mysql:host=192.168.1.155;dbname=elevators',     // Khomitch DB

           //'username' => 'Oper1',
	       //'dsn' => 'mysql:host=10.27.3.5;dbname=elevators',       // CDP DB

            'password' => 'lift',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            //'useFileTransport' => true,
            'transport'=>[
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'mailer.cds.glkh@gmail.com',
                'password' => 'Liftar2017',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
    ],
];
