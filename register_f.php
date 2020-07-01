<?php
    /*
    * The MIT License (MIT)
    *
    * Copyright (c) 2014-2020 yymartin
    *
    * This software may be modified and distributed under the terms
    * of the MIT license.  See the LICENSE file for details.
    */

    use Webauthn\PublicKeyCredentialCreationOptions;
    use Webauthn\PublicKeyCredentialRpEntity;
    use Webauthn\PublicKeyCredentialUserEntity;
    use Webauthn\Server;
    use Webauthn\PublicKeyCredentialSource;
    use Webauthn\PublicKeyCredentialSourceRepository;

    use Nyholm\Psr7\Factory\Psr17Factory;
    use Nyholm\Psr7Server\ServerRequestCreator;

    session_start();
    assert(isset($_SESSION['user_entity']) 
        && isset($_SESSION['creds_options']) 
        && isset($_SESSION['rp_entity'])
        && isset($_SESSION['repository']), 'Some values in $SESSION block the registration');
    
    header("Content-Type: application/json"); 
    $data = file_get_contents("php://input"); 
    $userEntity = PublicKeyCredentialUserEntity::createFromString($_SESSION['user_entity']);
    $publicKeyCredentialCreationOptions = PublicKeyCredentialCreationOptions::createFromString($_SESSION['creds_options']); 
    $rpEntity = PublicKeyCredentialRpEntity::createFromArray(json_decode($_SESSION['rp_entity'],True));
    $publicKeyCredentialSourceRepository = PublicKeyCredentialSourceRepository::createFromArray(json_decode($_SESSION['repository'],True));
    
    $psr17Factory = new Psr17Factory();
    $creator = new ServerRequestCreator(
        $psr17Factory, // ServerRequestFactory
        $psr17Factory, // UriFactory
        $psr17Factory, // UploadedFileFactory
        $psr17Factory  // StreamFactory
    );

    $serverRequest = $creator->fromGlobals();
    
    try {
        $server = new Server(
            $rpEntity,
            $publicKeyCredentialSourceRepository,
            null
        );

        $publicKeyCredentialSource = $server->loadAndCheckAttestationResponse(
            $data,
            $publicKeyCredentialCreationOptions, // The options you stored during the previous step
            $serverRequest                       // The PSR-7 request
        );

        $publicKeyCredentialSourceRepository->saveCredentialSource($publicKeyCredentialSource);

        unset($_SESSION['user_entity']);
        unset($_SESSION['creds_options']);
        unset($_SESSION['rp_entity']);
        unset($_SESSION['repository']);

        $_SESSION['registered'] = 'Done';
    } catch(Exception $exception) {
        echo $exception->getMessage();
        session_destroy();
    }
?>