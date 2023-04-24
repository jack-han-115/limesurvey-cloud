<?php

$blacklistConfig = [
// Number of violations per batch until filter activates
    'violationThreshold' => rand(3, 5),
// List of blacklisted words/sentences
    'blacklistEntries' => [
        'is waiting to be returned',
        'address is incorrect',
        'Claim Now',
        'shipment',
        'shipping',
        'smtpfox',
        'επιστροφή',
        'الخاصة ',
        'αναστολή',
        'να χρησιμοποιήσετε την κάρτα',
        'Tax return'
    ],
// time limit in minutes within the stored violation count for confirm and register email is still valid
    'timeLimit' => 2
];
