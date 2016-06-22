<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 07.01.16
 * Time: 11:29
 */

namespace Lava83\LavaProto\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Barryvdh\Debugbar\ServiceProvider as DebugBarServiceprovider;
use Barryvdh\Debugbar\Facade as DebugBarFacade;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageServiceProvider;
use Laracasts\Utilities\JavaScript\JavaScriptServiceProvider;
use Lava83\LavaProto\Exceptions\LogicException;
use Lava83\LavaProto\Repositories\CronRepository;
use Lava83\LavaProto\Repositories\CronRepostitoryEloquent;
use Lava83\LavaProto\Repositories\UserRepository;
use Lava83\LavaProto\Repositories\UserRepositoryEloquent;
use Prettus\Repository\Providers\RepositoryServiceProvider;

class LavaProtoServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/lava83-repositories.php' => config_path('lava83-repositories.php'),
        ]);
    }

    /**
     * Register the framework services
     *
     * @throws LogicException
     * @return void
     */
    public function register()
    {
        $this->app->register(LavaTwigServiceProvider::class);
        $this->app->register(LavaPluginServiceProvider::class);
        $this->app->register(LavaConsoleServiceProvider::class);
        $this->app->register(JavaScriptServiceProvider::class);
        /** @todo LavaRepositoryServiceProvider */
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->register(ImageServiceProvider::class);
        $this->app->alias('Image', Image::class);
        $this->bindRepositories();
        $this->registerDev();
        \Locale::setDefault(config('app.locale'));
    }

    protected function registerDev()
    {
        /*$env = $this->app->environment();
        if (in_array($env, ['dev', 'local'])) {
            $this->app->register(IdeHelperServiceProvider::class);
            $this->app->register(DebugBarServiceprovider::class);
            $this->app->alias('Debugbar', DebugBarFacade::class);
        }*/
    }

    protected function bindRepositories()
    {
        if (config('lava83-repositories.auto-bind-eloquent')) {
            $repositoriesPath = config('repository.generator.basePath') . DIRECTORY_SEPARATOR .
                config('repository.generator.paths.repositories');
            $rootNamespace = config('repository.generator.rootNamespace');
            if ($repositories = glob($repositoriesPath . DIRECTORY_SEPARATOR . '*Repository.php')) {
                foreach ($repositories as $repository) {
                    $className = basename($repository, '.php');
                    $interfaceName = $rootNamespace . 'Repositories\\' . $className;
                    $classNameEloquent = $rootNamespace . 'Repositories\\' . $className . 'Eloquent';
                    if (interface_exists($interfaceName)) {
                        $this->app->bindIf($interfaceName, $classNameEloquent);
                    }
                }
            }
        }
        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);
        $this->app->bind(CronRepository::class, CronRepostitoryEloquent::class);
    }
}
