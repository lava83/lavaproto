<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 07.01.16
 * Time: 11:29
 */

namespace Lava83\LavaProto\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Lava83\LavaProto\Exceptions\LogicException;
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
        if (in_array(config('cache.default'), ['file', 'database'])) {
            throw new LogicException('We use tagable caches. PLease dont use file or database as driver!');
        }

        $this->app->register(LavaTwigServiceProvider::class);
        $this->app->register(LavaPluginServiceProvider::class);
        $this->app->register(LavaConsoleServiceProvider::class);
        /** @todo LavaRepositoryServiceProvider */
        $this->app->register(RepositoryServiceProvider::class);
        $this->bindRepositories();
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
                        $this->app->bind($interfaceName, $classNameEloquent);
                    }
                }
            }
        }
        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);
    }
}
