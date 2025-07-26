<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Client as Google_Client;
use Google\Service\Gmail as Google_Service_Gmail;
use Illuminate\Support\Facades\Log;

class GmailApiAuthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gmail:authorize {code?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Authorize Gmail API and store refresh token.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new Google_Client();
        $client->setApplicationName(config('app.name'));
        $client->setScopes([Google_Service_Gmail::MAIL_GOOGLE_COM]);
        $client->setAuthConfig(storage_path('app/credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setRedirectUri(config('mail.mailers.gmail_api.redirect_uri'));

        $authUrl = $client->createAuthUrl();

        $this->info("Open the following link in your browser to authorize the application:");
        $this->comment($authUrl);
        $this->info("After authorization, copy the 'code' parameter from the redirect URL.");

        $code = $this->argument('code');

        if (!$code) {
            $code = $this->ask('Enter the authorization code you received:');
        }

        try {
            $accessToken = $client->fetchAccessTokenWithAuthCode($code);
            $client->setAccessToken($accessToken);

            // Save the token to a file
            $tokenPath = config('mail.mailers.gmail_api.access_token_path');
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));

            $this->info("Authorization successful! Token saved to: " . $tokenPath);
            if (isset($accessToken['refresh_token'])) {
                $this->info("Refresh token obtained and saved.");
            } else {
                $this->warn("No refresh token was obtained. This might happen if you've already authorized this application before without revoking access. If you need a new refresh token, ensure you revoke access in your Google account settings and try again.");
            }
        } catch (\Exception $e) {
            $this->error("Error during authorization: " . $e->getMessage());
            Log::error("Gmail API Authorization Error: " . $e->getMessage());
        }
    }
}
