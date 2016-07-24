---
view::extends: _includes.blog_post_base
view::yields: post_body
post::title: How to use PHP solarium in a Laravel project
post::brief: Think its hard to start with SOLR? Guess again; It is a breeze to start with SOLR within a Laravel project and I will show you how.
pageTitle: SOLR - Laravel integration of PHP Solarium - 
---

This is my second blog in a series about [SOLR](http://lucene.apache.org/solr/) with [PHP Solarium](https://github.com/solariumphp/solarium) library. My first blog was about the usage of OR filters to create [Multi-Select facets with SOLR](http://petericebear.github.io/php-solarium-multi-select-facets-20160720/).
With this blog item I will show you how easy it is to use the PHP Solarium library with the Laravel framework. 

## SOLR Instance
This blogpost will assume that you have a running SOLR instance somewhere, if you don't have a running SOLR instance then follow this guide to install one on a DigitalOcean droplet - [Install SOLR on Ubuntu](https://www.digitalocean.com/community/tutorials/how-to-install-solr-on-ubuntu-14-04).   

In this blogpost we will integrate the code of the first blogpost from [Multi-Select facets with SOLR](http://petericebear.github.io/php-solarium-multi-select-facets-20160720/) in a new Laravel project. 

## Lets start with a fresh Laravel project
```bash
laravel new solarium
```

If you are starting fresh with Laravel and you have an OSX machine, I would suggest you start with my [Starting with Laravel Valet on OSX](http://petericebear.github.io/starting-laravel-valet-on-osx-20160516/) tutorial to setup a quick local development environment.
You can test your new Laravel project in the browser at the url: http://solarium.dev/. 

## Add a solarium configuration
The Client of solarium library uses a injection of a configuration array. Make a new file named solarium.php in the config folder and add the following content to make this configuration array.

```php
<?php

return [
    'endpoint' => [
        'localhost' => [
            'host' => env('SOLR_HOST', '127.0.0.1'),
            'port' => env('SOLR_PORT', '8983'),
            'path' => env('SOLR_PATH', '/solr/'),
            'core' => env('SOLR_CORE', 'collection1')
        ]
    ]
];
```

This configuration assumes that the SOLR is installed on the local machine, uses the default port of 8983, the default solr install path and with a default collection named collection1.  
You can override every configuration setting within the .env file in your projectfolder. If you installed SOLR with the tutorial you can add the IP of the DigitalOcean droplet by adding this to .env file.

```php
SOLR_HOST=99.9.9.999
```

Where 99.9.9.999 is the IP of your droplet. If you want to know more about environment configuration please look at the guide on the Laravel site for the official [environment configuration](https://laravel.com/docs/5.2/configuration#environment-configuration).

## Install the PHP Solarium library

This library is the bread and butter for SOLR usage within PHP. This can be quickly installed by composer using the following command.

```bash
composer require solarium/solarium
```

At this time of writing it will install the version of 3.6.

## Lets boot up the Solarium Client

Within Laravel service providers are the central place of all the applications bootstrapping. Bootstrapping is nothing more than registering things, including registering service container bindings, event listeners, middleware, and even routes. Service providers are the central place to configure your application.
So lets make a service provider for registering the Solarium Client with our created solarium configuration file.

```bash
php artisan make:provider SolariumServiceProvider
```

This will create the file SolariumServiceProvider.php within your app/Providers folder. Open this file in your favourite editor and add the following contents:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Solarium\Client;

class SolariumServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Client::class, function ($app) {
            return new Client($app['config']['solarium']);
        });
    }

    public function provides()
    {
        return [Client::class];
    }
}
```

Now open up the file app/config/app.php and add the new SolariumServiceProvider to the other application providers.

```php
return [
    'providers' => [
        // List off others providers...
        App\Providers\SolariumServiceProvider::class,
    ]
];
```

Now every time you instantiate the Solarium\Client within your application it will inject the environment configuration. Notice the $defer setting, this setting makes sure that its not loaded at every request but only when you use the Client within your application. This will make you application faster if you don't always need the Client.
If your application needs it with every request remove that line from the service provider.

## Testing the integration

How can we test our Laravel application with SOLR that everything is setup correctly for querying SOLR? Well, SOLR has a built in ping feature so you can easily monitor your SOLR instance if its up or down.
So lets create a SolariumController with the Ping feature.

```php
php artisan make:controller SolariumController
```

This will add the Controller to the file app/Http/Controllers/SolariumController.php. Open this file in your favourite editor and add the following content.
 
```php
<?php

namespace App\Http\Controllers;

class SolariumController extends Controller
{
    protected $client;

    public function __construct(\Solarium\Client $client)
    {
        $this->client = $client;
    }

    public function ping()
    {
        // create a ping query
        $ping = $this->client->createPing();

        // execute the ping query
        try {
            $this->client->ping($ping);
            return response()->json('OK');
        } catch (\Solarium\Exception $e) {
            return response()->json('ERROR', 500);
        }
    }
}
```

Now lets create a ping route within the routes file. You can find the routes file at the default location app\Http\routes.php.

```php
Route::get('/ping', 'SolariumController@ping');
```

Now if you go the the URL of http://solarium.dev/ping it should return a string with the text 'OK'. If it returns the string 'ERROR' please check the IP, path and the name of the collection of the SOLR instance.

Now what is happening here?

In the constructor we use the IoC binding for the solarium client. Our created SolariumServiceProvider uses its binding and injects our environment configuration to the $client variable of the controller.

You can now use $this->client in the whole controller for tinkering with the Solarium library.

## Using the Multi-Select facets from our first blog

Now that we have a connection to our running SOLR instance. We can now test our [Multi-Select facets with SOLR](http://petericebear.github.io/php-solarium-multi-select-facets-20160720/) code by updating the Controller with the following content.

```php
<?php

namespace App\Http\Controllers;

class SolariumController extends Controller
{
    protected $client;

    public function __construct(\Solarium\Client $client)
    {
        $this->client = $client;
    }

    public function ping()
    {
        // create a ping query
        $ping = $this->client->createPing();

        // execute the ping query
        try {
            $this->client->ping($ping);
            return response()->json('OK');
        } catch (\Solarium\Exception $e) {
            return response()->json('ERROR', 500);
        }
    }
    
    public function search()
    {
        $query = $client->createSelect();
        $query->addFilterQuery(array('key'=>'provence', 'query'=>'provence:Groningen', 'tag'=>'include'));
        $query->addFilterQuery(array('key'=>'degree', 'query'=>'degree:MBO', 'tag'=>'exclude'));
        $facets = $query->getFacetSet();
        $facets->createFacetField(array('field'=>'degree', 'exclude'=>'exclude'));
        $resulset = $client->select($query);
        
        // display the total number of documents found by solr
        echo 'NumFound: ' . $resultset->getNumFound();
        
        // show documents using the resultset iterator
        foreach ($resultset as $document) {
        
            echo '<hr/><table>';
        
            // the documents are also iterable, to get all fields
            foreach ($document as $field => $value) {
                // this converts multivalue fields to a comma-separated string
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
        
                echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
            }
        
            echo '</table>';
        }
    }
}
```

Now add the search router to routes file. You can find the routes file at the default location app\Http\routes.php.

```php
Route::get('/search', 'SolariumController@search');
```

You are probably getting an error, this is because I already have a running instance with the fields and it is just for showing how easy it is to add the code from my first blog for using it into a Laravel project.
 
If you want to test SOLR search function with a clean install just removed these rows from the search function.

```php
// Remove these rows
$query->addFilterQuery(array('key'=>'provence', 'query'=>'provence:Groningen', 'tag'=>'include'));
$query->addFilterQuery(array('key'=>'degree', 'query'=>'degree:MBO', 'tag'=>'exclude'));
$facets = $query->getFacetSet();
$facets->createFacetField(array('field'=>'degree', 'exclude'=>'exclude'));
```

Now if you go the the URL of http://solarium.dev/search, It should return numFound: 0 in your browser.

In my next blog items I will try to explain how to create a custom schema.xml file to add your own fields to the SOLR instance and for searching in special fields like Spatial Search for finding results near a given location.

If you need a **fast** and **managed SOLR server** let me know at psteenbergen[at]gmail.com. I can hook you up with a **blazing fast** SOLR server starting at **€24,99 per month**.

Be well and till next time!