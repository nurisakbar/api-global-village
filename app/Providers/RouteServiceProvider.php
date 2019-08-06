<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
        $this->mapArticleRoutes();
        $this->mapVideoRoutes();
        $this->mapProductRoutes();
        $this->mapHarvestRoutes();
        $this->mapUserRoutes();
        $this->mapCategoryRoutes();
        $this->mapRegionRoutes();
        $this->mapUnitRoutes();
        $this->mapOrderRoutes();
        $this->mapLandRoutes();
        $this->mapBannerRoutes();
        $this->mapAdminRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/api.php'));
    }

    protected function mapAdminRoutes()
    {
        Route::prefix('admin')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/admin.php'));
    }

    protected function mapArticleRoutes()
    {
        Route::prefix('article')
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/article.php'));
    }

    protected function mapVideoRoutes()
    {
        Route::prefix('video')
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/video.php'));
    }

    protected function mapProductRoutes()
    {
        Route::prefix('product')
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/product.php'));
    }

    protected function mapHarvestRoutes()
    {
        Route::prefix('harvest')
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/harvest.php'));
    }

    protected function mapUserRoutes()
    {
        Route::prefix('user')
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/user.php'));
    }

    protected function mapCategoryRoutes()
    {
        Route::prefix('category')
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/category.php'));
    }

    protected function mapRegionRoutes()
    {
        Route::prefix('region')
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/region.php'));
    }

    protected function mapUnitRoutes()
    {
        Route::prefix('unit')
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/unit.php'));
    }

    protected function mapOrderRoutes()
    {
        Route::prefix('order')
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/order.php'));
    }

    protected function mapBannerRoutes()
    {
        Route::prefix('banner')
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/banner.php'));
    }

    protected function mapLandRoutes()
    {
        Route::prefix('land')
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/api/land.php'));
    }
}
