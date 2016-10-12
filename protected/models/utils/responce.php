<?php
function response($code, $data, $method = '') {
    switch ($code) {
      case '400' :
        header ( "HTTP/1.0 400 Bad Request" );
        break;
      case '401' :
        header ( "HTTP/1.0 401 Unauthorized" );
        break;
      case '403' :
        header ( "HTTP/1.0 403 Forbidden" );
        break;
      case '405' :
        header ( "HTTP/1.0 405 Method not allowed" );
        break;
      case '409' :
        header ( "HTTP/1.0 409 Conflict" );
        break;
    }
    header ( 'Content-Type: application/json' );
    
  
    if (! isset ( $data ['message'] ) && isset ( $data ['system_code'] )) {
      $data ['message'] = responseText ( $data , $method);
    }
    
    echo json_encode ( $data );
    exit ();
  }
  function responseText($data, $method = '') {
    $code = $data ['system_code'];
    $methods = array (
        'get' => 'Wählen',
        'post' => 'Einfügen',
        'put' => 'Aktualisieren',
        'patch' => 'Aktualisieren',
        'delete' => 'Löschen'
    );

    $methodsDone = array (
        'get'    => 'ausgewählt',
        'post'   => 'hinzugefügt',
        'put'    => 'aktualisiert',
        'patch'  => 'aktualisiert',
        'delete' => 'gelöscht'
    );

    $method = $method?$method:'get';
    $message = '';
    switch ($code) {
      case 'SUCCESSFUL' :
            $message = 'Erfolgreich ' . $methodsDone[$method];
        break;
      case 'ERR_NOT_EXISTS' :
            $message = $methods [$method] . ' fehlgeschlagen: Dieser Datensatz existiert nicht';
        break;
      case 'ERR_DUPLICATED' :
            $message = $methods [$method] . ' fehlgeschlagen: Dieser Datensatz existiert schon';
        break;
      case 'ERR_DUPLICATED_EMAIL' :
            $message = $methods [$method] . ' fehlgeschlagen: Diese E-Mail ist schon registriert';
        break;
      case 'ERR_DEPENDENT_RECORD' :
            $message = $methods [$method] . ' fehlgeschlagen: Es existieren ähnliche Datensätze: ' . $data ['table'] . '.';
        break;
      case 'ERR_INVALID_QUERY' :
            $message = $methods [$method] . ' fehlgeschlagen: Ungültige Abfrage';
        break;
      case 'ERR_QUERY' :
            $message = $methods [$method] . ' fehlgeschlagen: Query Fehler';
        break;
      case 'ERR_PERMISSION' :
            $message = $methods [$method] . ' fehlgeschlagen: Sie sind nicht zur Durchführung dieser Operation berechtigt';
        break;
      case 'ERR_ACCOUNT_PERMISSION' :
            $message = $methods [$method] . ' fehlgeschlagen: Sie sind nicht berechtigt, die Operation mit anderem Account durchzuführen';
        break;
      case 'ERR_MISSED_REQUIRED_PARAMETERS' :
            $message = $methods [$method] . ' fehlgeschlagen: Ein erforderlicher Parameter wurde für diese Anforderung nicht spezifiziert';
        break;
      case 'ERR_ID_NOT_SPECIFIED' :
            $message = $methods [$method] . ' fehlgeschlagen: ID ist nicht angegeben';
        break;
      case 'ERR_UPDATE_FORBIDDEN' :
            $message = $methods [$method] . ' fehlgeschlagen: Sie können diese Parameter nicht aktualisieren';
        break;
      default :
            $text = array (
                'LOGIN_SUCCESSFUL' => 'die Authentifizierung ist erfolgreich',
                'ERR_OUT_OF_DATE' => 'Token abgelaufen',
                'ERR_METHOD_NOT_ALLOWED' => 'Methode nicht erlaubt',
                'ERR_INVALID_TOKEN' => 'ungültiges Token ',
                'ERR_TOKEN_MISSED' => 'Auth Fehler',
                'ERR_RECOVERY_EMAIL' => 'E-Mail-Adresse nicht gültig',
                'ERR_SEND_EMAIL' => 'E-Mail-Sendefehler',
                'ERR_ACTIVATION_ACCAUNT' => 'Accountaktivierungsfehler',
                'ERR_USER_DISABLED' => 'Ihr Account ist deaktiviert',
                'ERR_AUTH_FAILED' => 'Die Authentifizierung fehlgeschlagen',
                'ERR_ACCOUNT_CREATION' => 'Accounterstellungsfehler',
                'ERR_SERVICE' => 'Ungültiger Serviceabruf',
            );
            $message = isset ( $text [$code] ) ? $text [$code] : $code;
    }
    return $message;
  }