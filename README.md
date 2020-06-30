# fido2-php

This repository is a proof of concept of a FIDO2 web server written in PHP.

## Documentation
This proof of concept is mostly based on the work of spomky-labs. The corresponding documentation can be found here: https://webauthn-doc.spomky-labs.com/

Full details of FIDO2 and especially webauthn can be found here: https://www.w3.org/TR/webauthn-1/

## Installation 
This proof of concept is built to work almost automatically using the following commands: 

```
git clone https://github.com/yymartin/fido2-php
cd fido2-php
php -S localhost:8000
```

## Usage 
Registration is accessible using http://localhost:8000/webapp/register.php <br>
Login is accessible using http://localhost:8000/webapp/login.php

## Warning
This work can freely be used for another usage and be adapted in a real FIDO2 webserver. To do so, HTTPS must be enabled to avoid any security vulnerabilities.
To enable HTTPS, simply uncomment assertions in files: 
- vendor/web-auth/webauthn-lib/src/AuthenticatorAttestationResponseValidator.php (line 116)
- vendor/web-auth/webauthn-lib/src/AuthenticatorAssertionResponseValidator.php (line 146)

## Licence
This work is released under [MIT license](https://github.com/yymartin/fido2-php/blob/master/LICENSE).
