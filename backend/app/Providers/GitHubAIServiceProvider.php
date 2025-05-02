<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GitHubOpenAIService;

class GitHubAIServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(GitHubOpenAIService::class, function ($app) {
            return new GitHubOpenAIService();
        });
    }

    public function boot()
    {
        //
    }
}