<?php
    /*
    * The MIT License (MIT)
    *
    * Copyright (c) 2014-2020 yymartin
    *
    * This software may be modified and distributed under the terms
    * of the MIT license.  See the LICENSE file for details.
    */

    namespace WebauthnServer;

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
    use Webauthn\PublicKeyCredentialRequestOptions;

    class Fido2Server {

    /**
     * @var PublicKeyCredentialRpEntity
     */
    private $rpEntity;

    /**
     * @var PublicKeyCredentialUserEntity
     */
    private $userEntity;

    /**
     * @var PublicKeyCredentialSourceRepository
     */
    private $publicKeyCredentialSourceRepository;

    /**
     * @var Server
     */
    private $server;

    public function __construct($serverName, $serverDomain, $userID, $userName, $userDisplayName, $publicKeyCredentialSourceRepository){
        $this->rpEntity = new PublicKeyCredentialRpEntity(
            $serverName,
            $serverDomain
        );

        $this->userEntity = new PublicKeyCredentialUserEntity(
            $userID,
            $userName,
            $userDisplayName
        );

        $this->publicKeyCredentialSourceRepository = $publicKeyCredentialSourceRepository;

        $this->server = new Server(
            $this->rpEntity,
            $this->publicKeyCredentialSourceRepository,
            null
        );
    }

    public function register() {
        assert((!isset($_SESSION['user_entity']) 
                && !isset($_SESSION['creds_options']) 
                && !isset($_SESSION['rp_entity'])
                && !isset($_SESSION['repository'])), 'Some values in $SESSION block the registration');
            
                $authenticatorSelectionCriteria = new AuthenticatorSelectionCriteria(
                null,
                false,
                AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_REQUIRED
            );
            
            $publicKeyCredentialCreationOptions = $this->server->generatePublicKeyCredentialCreationOptions(
                $this->userEntity,                                                                
                PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE, 
                [],
                $authenticatorSelectionCriteria                                                                                                                                                     
            );
            $_SESSION['user_entity'] = json_encode($this->userEntity->jsonSerialize());
            $_SESSION['creds_options'] = json_encode($publicKeyCredentialCreationOptions->jsonSerialize());
            $_SESSION['rp_entity'] = json_encode($this->rpEntity->jsonSerialize());
            $_SESSION['repository'] = json_encode($this->publicKeyCredentialSourceRepository->jsonSerialize());
            
            $json_value = $publicKeyCredentialCreationOptions->jsonSerialize();
            require('attestation.php');
    }

    public function login(){
        assert((!isset($_SESSION['user_entity']) 
                && !isset($_SESSION['creds_options']) 
                && !isset($_SESSION['rp_entity'])
                && !isset($_SESSION['repository'])), 'Some values in $SESSION block the login');            
        // Get the list of authenticators associated to the user
        $credentialSources = $this->publicKeyCredentialSourceRepository->findAllForUserEntity($this->userEntity);

        // Convert the Credential Sources into Public Key Credential Descriptors
        $allowedCredentials = array_map(function (PublicKeyCredentialSource $credential) {
            return $credential->getPublicKeyCredentialDescriptor();
        }, $credentialSources);

        // We generate the set of options.
        $publicKeyCredentialRequestOptions = $this->server->generatePublicKeyCredentialRequestOptions(
            PublicKeyCredentialRequestOptions::USER_VERIFICATION_REQUIREMENT_REQUIRED, 
            $allowedCredentials
        );

        $_SESSION['user_entity'] = json_encode($this->userEntity->jsonSerialize());
        $_SESSION['creds_options'] = json_encode($publicKeyCredentialRequestOptions->jsonSerialize());
        $_SESSION['rp_entity'] = json_encode($this->rpEntity->jsonSerialize());
        $_SESSION['repository'] = json_encode($this->publicKeyCredentialSourceRepository->jsonSerialize());

        $json_value = $publicKeyCredentialRequestOptions->jsonSerialize();
        require('assertion.php');  
    }
}
?>