<?php

return [
    'title' => 'Developer Integration',
    'intro' => '%s can be used to obtain a free PASA for your users. It can be used by exchanges or wallet developers or any other Pascal related software that needs their users to have a Pascal account.',
    'following' => 'The following table describes all available parameters that can be used for a request to %s.',
    'column_param' => 'Parameter',
    'column_description' => 'Description',
    'afac' => 'The account number where a certain amount of pasc will be sent to if the account transfer was successful. Read it as affiliate account number. <strong>Please omit the checksum</strong>. See <a href="%s">Affilitate Page</a> for more info.',
    'origin' => 'This value is for internal use to create statistics. You can use whatever value you want, but please stay with the same origin value in each request.',
    'phone_country_iso' => 'A 2 letter upper case ISO 3166 country code of the user related to the phone number (to select the correct phone region code). Click <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2">here</a> for a list of available codes.',
    'phone_country_number' => 'The international country calling code for the phone number. For example 1 for USA, 49 for germany and so on. Click <a href="https://en.wikipedia.org/wiki/List_of_country_calling_codes">here</a> for a complete list of codes.',
    'please_use' => 'Please either use <code style="display: inline-block">phone_country_iso</code> or <code style="display: inline-block">phone_country_number</code> Do not use both at the same time.',
    'phone' => 'The phone number of the user, <strong>without</strong> any country information.',
    'public_key' => 'The public key of the user. The PASA account will be transferred to this key.',
    'state' => 'An internal state value to make sure the returning request is from you, just like OAuth2 states.',
    'redirect' => 'An Url where the user will be redirected to after the PASA account was assigned to him.',
    'link_builder' => 'Link Builder',
    'label_afac' => 'Affiliate account number',
    'label_origin' => 'Origin',
    'label_phone_country_iso' => 'Phone country ISO code',
    'label_phone_country_number' => 'Phone international calling code',
    'label_phone_number' => 'Phone number',
    'label_public_key' => 'Public key',
    'label_state' => 'State',
    'label_redirect' => 'Redirect URL',
    'clipboard' => 'Copy to clipboard',
    'try_it' => 'Try it'
];
