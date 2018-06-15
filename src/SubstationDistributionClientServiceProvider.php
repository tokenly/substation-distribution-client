<?php

namespace Tokenly\SubstationDistributionClient;

use Illuminate\Support\ServiceProvider;
use Tokenly\SubstationClient\SubstationClient;

class SubstationDistributionClientServiceProvider extends ServiceProvider
{

    public function register()
    {
        // bind classes
        $this->app->bind(SubstationDistributionClient::class, function ($app) {
            return new SubstationDistributionClient($app->make(SubstationClient::class));
        });

    }

}
