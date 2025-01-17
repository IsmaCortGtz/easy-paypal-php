<?php

namespace IsmaCortGtz\EasyPayPalPhp\Request;

class VanillaRequest {
    private $username = null;
    private $password = null;

    static public function auth($username, $password) {
        return new VanillaRequest($username, $password);
    }

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function post($url, $body) {
        $basicAuth = "";
        if (gettype($body) !== "string") $body = json_encode($body);
        if ($this->username !== null && $this->password !== null) {
            $basicAuth = "Authorization: Basic ". base64_encode($this->username.":".$this->password) ."\r\n";
        }

        $options = [ 'http' => [
            'ignore_errors' => true,
            'method'        => 'POST',
            'content'       => $body,
            'header'        => "Content-Type: application/json\r\n" .
                               "Content-Length: ".strlen($body)."\r\n" .
                               $basicAuth
        ]];
        
        // Create the request
        $context = stream_context_create($options);
        $fp = fopen($url, 'r', false, $context);
        if (!$fp) return new VanillaResponse();

        // Read the response and close the connection
        $response = stream_get_contents($fp);
        $metaData = stream_get_meta_data($fp);
        fclose($fp);

        // Get the headers
        $headers = $metaData['wrapper_data'];

        preg_match('/HTTP\/\d\.\d (\d{3})/', $headers[0], $matches);
        $statusCode = intval($matches[1]); // Código de respuesta (por ejemplo, 200)
        $contentType = "";

        foreach ($headers as $header) {
            $header = strtolower($header);
            if (substr($header, 0, 13) !== "content-type:") continue;
            $header = str_replace(" ","", $header);
            $contentType = explode(":", $header)[1];
            break;
        }

        return new VanillaResponse($statusCode, $response, $contentType);
    }
}