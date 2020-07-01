<?php 
    /*
    * The MIT License (MIT)
    *
    * Copyright (c) 2014-2020 yymartin
    *
    * This software may be modified and distributed under the terms
    * of the MIT license.  See the LICENSE file for details.
    */
    require('../vendor/autoload.php');

    use Webauthn\PublicKeyCredentialRequestOptions;
    use Webauthn\PublicKeyCredentialUserEntity;
    use Webauthn\PublicKeyCredentialSource;
    use Webauthn\PublicKeyCredentialSourceRepository;
    use Webauthn\PublicKeyCredentialRpEntity;
    use Webauthn\Server;

    use Nyholm\Psr7\Factory\Psr17Factory;
    use Nyholm\Psr7Server\ServerRequestCreator;

    session_start();

    assert(isset($_SESSION['user_entity']) 
        && isset($_SESSION['creds_options']) 
        && isset($_SESSION['rp_entity'])
        && isset($_SESSION['repository']), 'Some values in $SESSION block the login');
        
    header("Content-Type: application/json"); 
    $data = file_get_contents("php://input"); 
    $userEntity = PublicKeyCredentialUserEntity::createFromString($_SESSION['user_entity']);
    $publicKeyCredentialRequestOptions = PublicKeyCredentialRequestOptions::createFromString($_SESSION['creds_options']); 
    $rpEntity = PublicKeyCredentialRpEntity::createFromArray(json_decode($_SESSION['rp_entity'],True));
    $publicKeyCredentialSourceRepository = PublicKeyCredentialSourceRepository::createFromArray(json_decode($_SESSION['repository'],True));

    $server = new Server(
        $rpEntity,
        $publicKeyCredentialSourceRepository,
        null
    );

    $psr17Factory = new Psr17Factory();
    $creator = new ServerRequestCreator(
        $psr17Factory, // ServerRequestFactory
        $psr17Factory, // UriFactory
        $psr17Factory, // UploadedFileFactory
        $psr17Factory  // StreamFactory
    );

    $serverRequest = $creator->fromGlobals();
    try {
        $publicKeyCredentialSource = $server->loadAndCheckAssertionResponse(
            $data,
            $publicKeyCredentialRequestOptions, // The options you stored during the previous step
            $userEntity,                        // The user entity
            $serverRequest                      // The PSR-7 request
        );
        
        unset($_SESSION['user_entity']);
        unset($_SESSION['creds_options']);
        unset($_SESSION['rp_entity']);
        unset($_SESSION['repository']);
        
        $_SESSION['logged_in'] = 'Done';
    } catch(Exception $exception) {
        echo $exception->getMessage();
        session_destroy();
    }
?>