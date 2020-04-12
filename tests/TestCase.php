<?php
namespace Djunehor\PutHelper\Test;

use Djunehor\PutHelper\PutHelperServiceProvider;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Orchestra\Testbench\Concerns\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            PutHelperServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetup($app)
    {

    }
}
