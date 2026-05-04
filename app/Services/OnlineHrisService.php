<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OnlineHrisService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.online_hris.url'), '/');
    }

    /**
     * Return a valid Bearer token, fetching one from the API if the stored token is blank.
     */
    public function getToken(): string
    {
        $token = (string) config('services.online_hris.token');

        if ($token !== '') {
            return $token;
        }

        $response = Http::timeout(15)
            ->post("{$this->baseUrl}/api/login", [
                'email'    => config('services.online_hris.email'),
                'password' => config('services.online_hris.password'),
            ]);

        if ($response->failed()) {
            Log::error('OnlineHrisService: login failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return '';
        }

        $token = (string) ($response->json('token') ?? '');

        if ($token !== '') {
            $this->writeTokenToEnv($token);
        }

        return $token;
    }

    /**
     * Fetch all unsynced attendance records from the remote API, following pagination.
     */
    public function fetchUnsyncedAttendances(): Collection
    {
        $token   = $this->getToken();
        $records = collect();

        if ($token === '') {
            Log::error('OnlineHrisService: cannot fetch attendances — no valid token.');
            return $records;
        }

        $url = "{$this->baseUrl}/api/attendances?is_synced=false";

        while ($url !== null) {
            $response = Http::timeout(15)
                ->withToken($token)
                ->get($url);

            if ($response->status() === 401) {
                $this->handleUnauthorized();
                $token = (string) config('services.online_hris.token');

                $response = Http::timeout(15)
                    ->withToken($token)
                    ->get($url);
            }

            if ($response->failed()) {
                Log::error('OnlineHrisService: failed to fetch attendance page', [
                    'url'    => $url,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                break;
            }

            $body = $response->json();
            $records = $records->concat($body['data'] ?? []);
            $url = $body['links']['next'] ?? null;
        }

        return $records;
    }

    /**
     * Mark a single remote attendance record as synced via PATCH.
     */
    public function markSynced(int $remoteId): void
    {
        $token = $this->getToken();

        if ($token === '') {
            Log::error('OnlineHrisService: cannot mark synced — no valid token.', ['remote_id' => $remoteId]);
            return;
        }

        $response = Http::timeout(15)
            ->withToken($token)
            ->patch("{$this->baseUrl}/api/attendances/{$remoteId}/mark-synced");

        if ($response->status() === 401) {
            $this->handleUnauthorized();
            $token = (string) config('services.online_hris.token');

            $response = Http::timeout(15)
                ->withToken($token)
                ->patch("{$this->baseUrl}/api/attendances/{$remoteId}/mark-synced");
        }

        if ($response->failed()) {
            Log::error('OnlineHrisService: failed to mark record synced on remote', [
                'remote_id' => $remoteId,
                'status'    => $response->status(),
                'body'      => $response->body(),
            ]);
        }
    }

    /**
     * Clear the stored token and re-authenticate to get a fresh one.
     */
    public function handleUnauthorized(): void
    {
        $this->writeTokenToEnv('');
        $this->getToken();
    }

    /**
     * Write (or clear) the token in the .env file and update the running config.
     */
    private function writeTokenToEnv(string $token): void
    {
        $envPath  = base_path('.env');
        $contents = file_get_contents($envPath);

        if ($contents === false) {
            Log::error('OnlineHrisService: could not read .env file when writing token.');
            return;
        }

        $contents = preg_replace(
            '/^ONLINE_HRIS_TOKEN=.*/m',
            'ONLINE_HRIS_TOKEN=' . $token,
            $contents
        );

        file_put_contents($envPath, $contents);
        config(['services.online_hris.token' => $token]);
    }
}
