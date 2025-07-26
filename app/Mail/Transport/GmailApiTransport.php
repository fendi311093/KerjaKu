<?php

namespace App\Mail\Transport;

use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;

class GmailApiTransport extends Transport
{
    /**
     * The Gmail API client instance.
     *
     * @var \Google_Service_Gmail
     */
    protected $gmailService;

    /**
     * The Google Client instance.
     *
     * @var \Google_Client
     */
    protected $client;

    /**
     * Create a new Gmail API transport instance.
     *
     * @param  \Google_Service_Gmail  $gmailService
     * @param  \Google_Client  $client
     * @return void
     */
    public function __construct(Google_Service_Gmail $gmailService, Google_Client $client)
    {
        $this->gmailService = $gmailService;
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $rawMessage = $this->createRawMessage($message);

        $gmailMessage = new Google_Service_Gmail_Message();
        $gmailMessage->setRaw($rawMessage);

        try {
            $this->gmailService->users->messages->send('me', $gmailMessage);
        } catch (\Exception $e) {
            // Handle specific errors, e.g., token expired
            if ($e->getCode() == 401 && $this->client->getRefreshToken()) {
                // Attempt to refresh token and retry
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                file_put_contents(config('mail.mailers.gmail_api.access_token_path'), json_encode($this->client->getAccessToken()));
                // Retry sending the email
                $this->gmailService->users->messages->send('me', $gmailMessage);
            } else {
                throw $e;
            }
        }

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * Create the raw message for the Gmail API.
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @return string
     */
    protected function createRawMessage(Swift_Mime_SimpleMessage $message)
    {
        // Set the From header if it's not already set by SwiftMailer
        if (empty($message->getFrom())) {
            $message->setFrom(config('mail.from.address'), config('mail.from.name'));
        }

        return rtrim(strtr(base64_encode($message->toString()), '+/', '-_'), '=');
    }
}