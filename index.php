<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zawwij Mail</title>
</head>
<body>
    <h1>Welcome</h1>
    <?php

        include 'password.php';

        $Params=array(
            'kas_login' => 'w019e957',          // das KAS-Login
            'kas_auth_type' => 'sha1',
            'kas_auth_data' => sha1(echo $password),// das KAS-Passwort
            'session_lifetime' => 600,         // Gültigkeit des Tokens in Sekunden
            'session_update_lifetime' => 'Y'
        );

        try {
            $SoapLogon = new SoapClient('https://kasapi.kasserver.com/soap/wsdl/KasAuth.wsdl');
            $CredentialToken = $SoapLogon->KasAuth(json_encode($Params));
            echo "Ihr SessionToken lautet: $CredentialToken"; //  bd6d56c7a992c53e521410ef067e13dc
        }

        // Fehler abfangen und ausgeben
        catch (SoapFault $fault) {
            trigger_error("Fehlernummer: {$fault->faultcode},
                            Fehlermeldung: {$fault->faultstring},
                            Verursacher: {$fault->faultactor},
                            Details: {$fault->detail}", E_USER_ERROR
                        );
        }




        if(isset($_POST['submit'])) {

            try
            {
            // Parameter für die API-Funktion
            $Params = array(  'mail_password' => $_POST['password'],
                                'local_part' => split('@', $_POST['email'])[0],
                                'domain_part' => 'zawwij.com',
                                'responder' => 'N',
                                'responder_text' => '0',
                                'copy_adress' => '',
                                'mail_sender_alias' => 'Ich'
                            );

            $SoapRequest = new SoapClient('https://kasapi.kasserver.com/soap/wsdl/KasApi.wsdl');
            $req = $SoapRequest->KasApi(json_encode(array(
                        'kas_login' => $kas_user,                // KAS-User
                        'kas_auth_type' => 'session',             // Auth per Sessiontoken
                        'kas_auth_data' => $CredentialToken,      // Auth-Token
                        'kas_action' => 'add_mailaccount',      // API-Funktion
                        'KasRequestParams' => $Params          // Parameter an die API-Funktion
                        )));
            }

            // Fehler abfangen und ausgeben
            catch (SoapFault $fault)
            {
                trigger_error(" Fehlernummer: {$fault->faultcode},
                                Fehlermeldung: {$fault->faultstring},
                                Verursacher: {$fault->faultactor},
                                Details: {$fault->detail}", E_USER_ERROR);
            }
        }

    ?>
    <form action="index.php" method="post">
        <label for="email">EMail:</label><br>
        <input type="text" id="email" name="email" onchange="mailInputChange()" placeholder="mail@zawwij.com" require><br>
        <label for="password">Password:</label><br>
        <input type="password" name="password" id="password" require>
        <input type="submit" value="Create EMail">
    </form>

    <script src="script.js"></script>
</body>
</html>
