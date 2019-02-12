<?php

namespace App\Watson;

use GuzzleHttp\Client;

class Assistant
{
    private $username;
    private $password;
    private $workspace;

    public function __construct(string $username, string $password, string $workspace)
    {
        $this->username = $username;
        $this->password = $password;
        $this->workspace = $workspace;
    }

    public function dialog(string $message, $context = null)
    {
        $client = new Client;

        if (!$context) {
            $context = new \stdClass;
        }

        $url = 'https://gateway.watsonplatform.net/assistant/api/v1/workspaces/' . $this->workspace . '/message?version=2018-09-20';
        $response = $client->request('POST', $url, [
            'json' => [
                'input' => [
                    'text' => $message
                ],
                'context' => $context
            ],
            'auth' => [$this->username, $this->password]
        ]);

        return (string)$response->getBody();
    }
}
