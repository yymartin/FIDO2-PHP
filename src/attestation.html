<!doctype html>
<!--
   The MIT License (MIT)
  
   Copyright (c) 2014-2020 yymartin
  
   This software may be modified and distributed under the terms
   of the MIT license.  See the LICENSE file for details.
 
 -->

<head>
  <meta charset="utf-8">
</head>

<body>
    <script>
    var js_data = '<?php echo json_encode($json_value); ?>';
    const publicKey = JSON.parse(js_data);
    function arrayToBase64String(a) {
        return btoa(String.fromCharCode(...a));
    }

    function base64url2base64(input) {
        input = input
            .replace(/=/g, "")
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const pad = input.length % 4;
        if(pad) {
            if(pad === 1) {
                throw new Error('InvalidLengthError: Input base64url string is the wrong length to determine padding');
            }
            input += new Array(5-pad).join('=');
        }

        return input;
    }

    publicKey.challenge = Uint8Array.from(window.atob(base64url2base64(publicKey.challenge)), function(c){return c.charCodeAt(0);});
    publicKey.user.id = Uint8Array.from(window.atob(publicKey.user.id), function(c){return c.charCodeAt(0);});
    if (publicKey.excludeCredentials) {
        publicKey.excludeCredentials = publicKey.excludeCredentials.map(function(data) {
            data.id = Uint8Array.from(window.atob(base64url2base64(data.id)), function(c){return c.charCodeAt(0);});
            return data;
        });
    }

    navigator.credentials.create({'publicKey': publicKey })
        .then(function(data){
            const publicKeyCredential = {
                id: data.id,
                type: data.type,
                rawId: arrayToBase64String(new Uint8Array(data.rawId)),
                response: {
                    clientDataJSON: arrayToBase64String(new Uint8Array(data.response.clientDataJSON)),
                    attestationObject: arrayToBase64String(new Uint8Array(data.response.attestationObject))
                }
            };

            var xhr = new XMLHttpRequest();
            var url = "vendor/yymartin/fido2-php/src/register_f.php";
            xhr.open("POST", url, true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var result = xhr.response;
                    console.log(result);
                    if (result === ''){
                        window.location.href= window.location.pathname;
                    } else {
                        console.log(xhr.response);
                        alert('Open your browser console!');
                    }
                }
            };
            var data = JSON.stringify(publicKeyCredential);
            xhr.send(data);
        })
        .catch(function(error){
            alert('Open your browser console!');
            console.log('FAIL', error);
        });
    </script>
</body>
</html>
