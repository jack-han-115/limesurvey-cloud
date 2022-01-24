<?php

/** @var integer Number of violations per batch until filter activates */
$violationThreshold = rand(3, 5);

/** @var  array List of blacklisted words/sentences */
$blacklistEntries = [
    'is waiting to be returned',
    'address is incorrect',
    'Claim Now',
    'gift card',
    'Express shipment',
    'gift',
    'shipping',
    'smtpfox',
    'tracking number',
    'επιστροφή',
    'αναστολή',
    'να χρησιμοποιήσετε την κάρτα',
    'Tax return'
];
