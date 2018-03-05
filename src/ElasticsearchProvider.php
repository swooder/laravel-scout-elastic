<?php

namespace Woodfish\Elasticsearch;

use Laravel\Scout\EngineManager;
use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder as ElasticBuilder;
use Woodfish\Elasticsearch\Console\ElasticIndicesCommand;
use Woodfish\Elasticsearch\Console\ElasticMakeIndicesCommand;

class ElasticsearchProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

    }

    public function register()
    {
        if ($this->app instanceof LumenApplication) {
            $this->app->configure('elasticsearch');
        }

        app(EngineManager::class)->extend('elasticsearch', function($app) {
            return new ElasticsearchEngine(ElasticBuilder::create()
                ->setHosts(config('elasticsearch.hosts'))
                ->build(),
                config('elasticsearch.queries')
            );
        });


        if ($this->app->runningInConsole()) {
            $this->commands([
                ElasticIndicesCommand::class,
                ElasticMakeIndicesCommand::class
            ]);

            if (function_exists('config_path')) {
                $publishPath = config_path('elasticsearch.php');
            } else {
                $publishPath = base_path('config/elasticsearch.php');
            }
            $this->publishes([
                __DIR__ . '/../config/elasticsearch.php' => $publishPath,
            ], 'config');

        }
    }
}
