<?php
namespace Djunehor\PutHelper;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;

class PutHelperServiceProvider extends ServiceProvider
{


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $kernel = $this->app->make('Illuminate\Contracts\Http\Kernel');
        $kernel->pushMiddleware(PutRequestMiddleware::class);
    }

    protected $message = 'Mustr be a file';

    /**
     * Publishes all the config file this package needs to function.
     */
    public function boot()
    {
        Validator::extend('put_file', function ($attribute, $value, $parameters, $validator) {
            return (is_object($value) && ($value && get_class($value) == UploadedFile::class));
            $this->message = "$attribute must be a file!";
        }, $this->message);
    }

}
