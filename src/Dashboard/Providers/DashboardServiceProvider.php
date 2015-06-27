<?php

namespace Artesaos\Defender\Providers;

use Illuminate\Support\ServiceProvider;
use Artesaos\Defender\Repositories\Eloquent\EloquentUserRepository;

class DashboardServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->registerRoutes();
        $this->loadDashboardViews();
        $this->loadDashboardTranslations();
        $this->publishDashboardConfiguration();
        $this->publishDashboardAssets();
    }

    public function register()
	{
		$this->registerBindings();

		if ( ! isset($this->app['flash']))
		{
			$this->app->register('Laracasts\Flash\FlashServiceProvider');
		}
	}

    protected function registerBindings()
    {
        $userClass = $this->app['config']->get('auth.model');

        $this->app->singleton(
            'defender.user', function ($app) use ($userClass) {
                return new EloquentUserRepository($app, $app->make($userClass));
            }
        );

        $this->app->singleton(
            'Artesaos\Defender\Contracts\UserRepository', function ($app) {
                return $app['defender.user'];
            }
        );
    }

    /**
     * Register routes.
     */
    protected function registerRoutes()
    {
        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app['router'];

        //TODO: Made that prefix configurable
        
        $router->group(['prefix' => 'defender'], function () use ($router) {
            require __DIR__.'/../../resources/routes.php';
        });
    }

    /**
     * Publish dashboard configuration.
     */
    protected function publishDashboardConfiguration()
    {
		# Publish config
		# Settings like prefix definition
    }

    protected function publishDashboardAssets()
    {
        $this->publishes([
            __DIR__.'/../../resources/assets' => public_path('artesaos/dashboard')
        ], 'assets');
    }

    /**
     * Load dashboard views.
     */
    protected function loadDashboardViews()
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views/artesaos', 'artesaos');
    }

    protected function loadDashboardTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang/artesaos', 'artesaos');
    }

	public function provides()
	{
		return ['defender.user'];
	}
}