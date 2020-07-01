# fido2-php

This repository contains a PHP library to implement a Webauthn server

## Documentation
This library is mostly based on the work of spomky-labs. The corresponding documentation can be found here: https://webauthn-doc.spomky-labs.com/

Full details of FIDO2 and especially webauthn can be found here: https://www.w3.org/TR/webauthn-1/

## Installation 
The library can be installed using composer

```
composer require yymartin/fido2-php
```

## Usage 
// TODO

## Warning
If you are developping your project, you are likely to run the server locally and you may need to disable HTTPS. 
To do so, simply comment assertions in files: 
- vendor/web-auth/webauthn-lib/src/AuthenticatorAttestationResponseValidator.php (line 116)
- vendor/web-auth/webauthn-lib/src/AuthenticatorAssertionResponseValidator.php (line 146)

## Licence
This work is released under [MIT license](https://github.com/yymartin/fido2-php/blob/master/LICENSE).
