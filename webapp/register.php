<?php
    require('../vendor/autoload.php');

    use Cose\Algorithms;
    use Webauthn\AuthenticatorSelectionCriteria;
    use Webauthn\PublicKeyCredentialDescriptor;
    use Webauthn\PublicKeyCredentialCreationOptions;
    use Webauthn\PublicKeyCredentialParameters;
    use Webauthn\PublicKeyCredentialRpEntity;
    use Webauthn\PublicKeyCredentialUserEntity;
    use Webauthn\Server;
    use Webauthn\PublicKeyCredentialSource;
    use Webauthn\PublicKeyCredentialSourceRepository;

    use Nyholm\Psr7\Factory\Psr17Factory;
    use Nyholm\Psr7Server\ServerRequestCreator;

    session_start();

    $rpEntity = new PublicKeyCredentialRpEntity(
        'localhost',
        'localhost'
    );

    $publicKeyCredentialSourceRepository = new PublicKeyCredentialSourceRepository();

    $server = new Server(
        $rpEntity,
        $publicKeyCredentialSourceRepository,
        null
    );

    if(!isset($_SESSION['user_entity']) && !isset($_SESSION['creds_options'])){
        $userEntity = new PublicKeyCredentialUserEntity(
            'id',
            'username',
            'display name'
        );

        $authenticatorSelectionCriteria = new AuthenticatorSelectionCriteria(
            null,
            false,
            AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_REQUIRED
        );
        
        $publicKeyCredentialCreationOptions = $server->generatePublicKeyCredentialCreationOptions(
            $userEntity,                                                                
            PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE, 
            [],
            $authenticatorSelectionCriteria                                                                                                                                                     
        );
        $_SESSION['user_entity'] = json_encode($userEntity->jsonSerialize());
        $_SESSION['creds_options'] = json_encode($publicKeyCredentialCreationOptions->jsonSerialize());
        $json_value = $publicKeyCredentialCreationOptions->jsonSerialize();
        require('attestation.php');
    } else {
        header("Content-Type: application/json"); 
        $data = file_get_contents("php://input"); 
        $userEntity = PublicKeyCredentialUserEntity::createFromString($_SESSION['user_entity']);
        $publicKeyCredentialCreationOptions = PublicKeyCredentialCreationOptions::createFromString($_SESSION['creds_options']); 
    
        $psr17Factory = new Psr17Factory();
        $creator = new ServerRequestCreator(
            $psr17Factory, // ServerRequestFactory
            $psr17Factory, // UriFactory
            $psr17Factory, // UploadedFileFactory
            $psr17Factory  // StreamFactory
        );
    
        $serverRequest = $creator->fromGlobals();
    
        try {
            $publicKeyCredentialSource = $server->loadAndCheckAttestationResponse(
                $data,
                $publicKeyCredentialCreationOptions, // The options you stored during the previous step
                $serverRequest                       // The PSR-7 request
            );

            $publicKeyCredentialSourceRepository->saveCredentialSource($publicKeyCredentialSource);

            session_destroy();

        } catch(Exception $exception) {
            echo $exception->getMessage();
        }
    }
?>