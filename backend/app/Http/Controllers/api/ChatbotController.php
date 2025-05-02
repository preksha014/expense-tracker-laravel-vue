<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\GitHubOpenAIService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    protected $githubAIService;

    public function __construct(GitHubOpenAIService $githubAIService)
    {
        $this->githubAIService = $githubAIService;
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        // You can optionally allow the frontend to specify a system prompt
        $systemPrompt = $request->input('systemPrompt', 'You are a helpful assistant.');
        
        $response = $this->githubAIService->generateChatResponse(
            $request->message,
            $systemPrompt
        );

        return response()->json($response);
    }
}