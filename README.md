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
### Implementation of PublicKeyCredentialSourceRepository
The first thing you need to do is to implement a class : PublicKeyCredentialSourceRepository

A PublicKeyCredentialSource is an object representing an authenticator. The way you store and manage this object depends on the implementation of each project. Hence, you need to implement it yourself and put this file at location : vendor/web-auth/webauthn-lib/src/PublicKeyCredentialSourceRepository.php

An exemple is provided here but it is not recommended to use it in a production environment since it stores PublicKeyCredentialSource in a file. 

### Usage of Fido2Server
Fido2Server is an object representing the Webauthn server. An instance requires elements from your server and from the user you want to register/login. Then, you can call the function register() or login(). If the function works correctly, SESSION values SESSION['registered'] or SESSION['logged_in'] are set, otherwise, an error message is displayed. 

An exemple is provided using the following:

```
<?php
    require('./vendor/autoload.php');

    use WebauthnServer\FIDO2server;
    use Webauthn\PublicKeyCredentialSourceRepository;
    use Webauthn\PublicKeyCredentialUserEntity;

    $serverName = 'example';
    $serverDomain = 'www.example.com';

    $userID = '1';
    $userName = 'username';
    $userDisplayName = 'Display Name';

    $publicKeyCredentialSourceRepository = new PublicKeyCredentialSourceRepository();

    session_start();
    if (!isset($_SESSION['registered']) && !isset($_SESSION['logged_in'])){
        $server = new FIDO2Server($serverName, $serverDomain, $userID, $userName, $userDisplayName, $publicKeyCredentialSourceRepository);
        $server->register(); 
    } elseif(isset($_SESSION['registered']) && !isset($_SESSION['logged_in'])){
        $server = new FIDO2Server($serverName, $serverDomain, $userID, $userName, $userDisplayName, $publicKeyCredentialSourceRepository);
        $server->login();
    } else {
        echo 'Registered and logged in!';
    }
?>
```

## Warning
If you are developping your project, you are likely to run the server locally and you may need to disable HTTPS. 
To do so, simply comment assertions in files: 
- vendor/web-auth/webauthn-lib/src/AuthenticatorAttestationResponseValidator.php (line 116)
- vendor/web-auth/webauthn-lib/src/AuthenticatorAssertionResponseValidator.php (line 146)

## Licence
This work is released under [MIT license](https://github.com/yymartin/fido2-php/blob/master/LICENSE).
