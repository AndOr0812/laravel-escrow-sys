<?php

namespace VSoft\LaravelEscrow\Tests;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use VSoft\LaravelCurrencies\Amount;
use VSoft\LaravelEscrow\Escrow;
use VSoft\LaravelEscrow\EscrowFacade;
use VSoft\LaravelEscrow\Providers\EscrowServiceProvider;
use VSoft\LaravelEscrow\Tests\Fakes\Customer;
use VSoft\LaravelEscrow\Tests\Fakes\Provider;
use VSoft\LaravelEscrow\Transaction;
use VSoft\LaravelStripeObjects\Providers\StripeObjectsServiceProvider;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpFactories($this->app);

        // Put Amount in test mode so we don't need a currency implementation
        Amount::test();
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        putenv('APP_ENV=testing');
        putenv('APP_DEBUG=true');
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');

        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->useEnvironmentPath(__DIR__.'/..');
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $app->register(EscrowServiceProvider::class);
        $app->register(StripeObjectsServiceProvider::class);
        $app->afterResolving('migrator', function ($migrator) {
            $migrator->path(__DIR__.'/migrations/');
        });

        // Register facade
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Escrow', EscrowFacade::class);

        return $app;
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpFactories($app)
    {
        $app->make(Factory::class)->define(Transaction::class, function ($faker) {
            return [
                'source_type' => 'foo',
                'source_id' => 1,
                'destination_type' => 'bar',
                'destination_id' => 1,
                'amount' => rand(100, 1000),
                'currency_code' => Amount::zero()->currency()->getCode(),
            ];
        });

        $app->make(Factory::class)->define(Customer::class, function ($faker) {
            return [
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => bcrypt('foo'),
            ];
        });

        $app->make(Factory::class)->define(Escrow::class, function ($faker) {
            return [
                'escrowable_type' => 'foo',
                'escrowable_id' => 1,
                'customer_type' => 'bar',
                'customer_id' => 1,
                'provider_type' => 'baz',
                'provider_id' => 1,
            ];
        });

        $app->make(Factory::class)->define(Provider::class, function ($faker) {
            return [
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => bcrypt('foo'),
            ];
        });
    }
}
