<?php

namespace App\Services;

use OpenAI;
use Illuminate\Support\Facades\Log;
use Exception;

class GitHubOpenAIService
{
    protected $client;
    protected $modelName;

    public function __construct()
    {
        $token = config('services.github.token');
        $endpoint = config('services.github.api_url');
        $this->modelName = config('services.github.model_name', 'openai/gpt-4o');

        $this->client = OpenAI::factory()
            ->withBaseUri($endpoint)
            ->withApiKey($token)
            ->make();
    }

    public function generateChatResponse(string $message, string $systemPrompt = "You are a helpful assistant.")
    {
        try {
            $response = $this->client->chat()->create([
                'model' => $this->modelName,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message]
                ],
                'temperature' => 1.0,
                'top_p' => 1.0,
                'max_tokens' => 1000
            ]);

            // Extract the response content
            return [
                'error' => false,
                'response' => $response->choices[0]->message->content
            ];
        } catch (Exception $e) {
            Log::error('GitHub AI API Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }
}