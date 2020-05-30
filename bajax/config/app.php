<?php

return [
    'client_server' => [
        "resetpassword"=>env('CLIENT_RESET_PASSWORD', 'http://localhost:8080/local/resetpassword/'),
        "verifyemail"=>env('CLIENT_VERIFY_EMAIL', 'http://localhost:8080/local/verifyemail/'),
    ],
];
