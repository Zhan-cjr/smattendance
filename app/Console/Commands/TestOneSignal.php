<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestOneSignal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:onesignal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test OneSignal API Connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appId = config('services.onesignal.app_id');
        $restApiKey = config('services.onesignal.rest_api_key'); 

        $this->info("Testing OneSignal API...");
        $this->info("App ID: $appId");
        $this->info("REST API Key: $restApiKey");

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $restApiKey,
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => $appId,
                'included_segments' => ['All'],
                'headings' => ['en' => 'Test Notification'],
                'contents' => ['en' => 'This is a test message from CLI'],
            ]);

            $this->info("Status Code: " . $response->status());
            $this->info("Response Body: " . $response->body());

        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
        }
    }
}
