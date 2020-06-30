<?php   
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

    $userEntity = new PublicKeyCredentialUserEntity(
        'id',
        'username',
        'display name'
    );

    if(!isset($_SESSION['user_entity']) && !isset($_SESSION['creds_options'])){        
        // Get the list of authenticators associated to the user
        $credentialSources = $publicKeyCredentialSourceRepository->findAllForUserEntity($userEntity);

        // Convert the Credential Sources into Public Key Credential Descriptors
        $allowedCredentials = array_map(function (PublicKeyCredentialSource $credential) {
            return $credential->getPublicKeyCredentialDescriptor();
        }, $credentialSources);

        // We generate the set of options.
        $publicKeyCredentialRequestOptions = $server->generatePublicKeyCredentialRequestOptions(
            PublicKeyCredentialRequestOptions::USER_VERIFICATION_REQUIREMENT_REQUIRED, 
            $allowedCredentials
        );

        $_SESSION['user_entity'] = json_encode($userEntity->jsonSerialize());
        $_SESSION['creds_options'] = json_encode($publicKeyCredentialRequestOptions->jsonSerialize());
        $json_value = $publicKeyCredentialRequestOptions->jsonSerialize();
        require('assertion.php');
    } else {
        header("Content-Type: application/json"); 
        $data = file_get_contents("php://input"); 
        $userEntity = PublicKeyCredentialUserEntity::createFromString($_SESSION['user_entity']);
        $publicKeyCredentialRequestOptions = PublicKeyCredentialRequestOptions::createFromString($_SESSION['creds_options']); 

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
            
            session_destroy();
        } catch(Exception $exception) {
            echo $exception->getMessage();
        }
    }  
?>