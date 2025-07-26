<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GmailApiMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('gmail.client', function ($app) {
            $client = new \Google_Client();
            $client->setApplicationName(config('app.name'));
            $client->setScopes([\Google_Service_Gmail::MAIL_GOOGLE_COM]);
            $client->setAuthConfig(storage_path('app/credentials.json')); // Path to your downloaded credentials.json
            $client->setAccessType('offline');
            $client->setPrompt('select_account consent');

            // Load previously authorized token from a file, if it exists.
            // The file token.json stores the user's access and refresh tokens, and is
            // created automatically when the authorization flow completes for the first
            // time.
            $tokenPath = config('mail.mailers.gmail_api.access_token_path');
            if (file_exists($tokenPath)) {
                $accessToken = json_decode(file_get_contents($tokenPath), true);
                $client->setAccessToken($accessToken);
            }

            // If there is no previous token or it's expired.
            if ($client->isAccessTokenExpired()) {
                // Refresh the token if possible, else fetch a new one.
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                } else {
                    // Request authorization from the user.
                    $authUrl = $client->createAuthUrl();
                    // You'll need to open this URL in your browser and authorize the app.
                    // After authorization, Google will redirect to your GOOGLE_REDIRECT_URI
                    // with a 'code' parameter. You'll need to handle this code to get the
                    // access token. This part usually involves a web route.
                    // For CLI/background jobs, you'll need to manually get the token once.
                    // For now, we'll just log the URL.
                    \Log::error("Open the following link in your browser to authorize the application: " . $authUrl);
                    throw new \Exception("Google API authorization required. Please check your logs for the authorization URL.");
                }
                // Save the token to a file.
                if (!file_exists(dirname($tokenPath))) {
                    mkdir(dirname($tokenPath), 0700, true);
                }
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            }
            return $client;
        });

        $this->app->singleton('gmail.service', function ($app) {
            return new \Google_Service_Gmail($app['gmail.client']);
        });

        }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app['mail.manager']->extend('gmail_api', function ($app) {
            return new \App\Mail\Transport\GmailApiTransport(
                $app['gmail.service'],
                $app['gmail.client']
            );
        });
    }
}
