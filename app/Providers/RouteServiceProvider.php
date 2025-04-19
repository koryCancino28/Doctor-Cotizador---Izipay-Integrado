<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * El espacio de nombres para las rutas del controlador.
     *
     * @var string
     */
    public const CONTROLLER_NAMESPACE = 'App\\Http\\Controllers';

    /**
     * Definir las rutas para la aplicación.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Definir las rutas de la API.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api') // las rutas estarán bajo el prefijo 'api'
             ->middleware('api') // middleware 'api' se aplica a las rutas
             ->namespace($this->namespace) // usa el namespace de los controladores
             ->group(base_path('routes/api.php')); // carga el archivo de rutas api.php
    }

    /**
     * Definir las rutas web.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }
}
