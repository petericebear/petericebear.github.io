---
view::extends: _includes.blog_post_base
view::yields: post_body
post::title: Starting with Laravel Echo and PusherJS
post::brief: Get started with Laravel Echo in Laravel for realtime chat functions using PusherJS for the socket connection.
pageTitle: Starting Laravel Echo and PusherJS - realtime message updates - 
---------------------------------------------------

I was excited from day 1 to try out Laravel Echo. Unfortunately I ran into an issue and stopped working with it and build the needed custom code for realtime updates.
But last week I found some time to start fiddling with it once again, and I am hooked. 

The issue I had came back but now with the extra time I found the solution and THAT is the reason I wrote this blog item. So lets begin to bring to excitement from Laravel Echo also to you.

## Create a new Laravel project

```bash
laravel new laravelecho
```

This will check out a new installation of laravel into the folder laravelecho. If you do not have the Laravel installed you can run "composer create-project laravel/laravel laravelecho".

## Laravel Echo prerequisites

Install the npm prerequisites for using Laravel Echo and the pusherJS library.

```bash
cd laravelecho
npm install --save laravel-echo pusher-js
```

## Setup the configurations

Open "resources/assets/js/bootstrap.js" in your favourite editor.
In the bottom of the file uncomment the last block from the file.

```js
// Uncomment these rows
// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });

// To
import Echo from "laravel-echo"

window.Echo = new Echo({
     broadcaster: 'pusher',
     key: 'your-pusher-key'
});
```

If you are located in the EU like me, change the last block with the element "cluster: 'eu'" and if you want the connection to be encrypted (what you should) set the encrypted flag to true.

```js
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'your-pusher-key',
    cluster: 'eu',
    encrypted: true
});
```

Be sure to replace the 'your-pusher-key' with the key provided from your PusherJS account after registration of your application at their website.

Now open the file "config/broadcasting.php" and change the following lines within the options element. If you did not added the cluster or encryption in the previous section you can skip this step.

```php
<?php
    // ...
    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => 'eu',
                'encrypted' => true,
            ],
        ],
        
    // ... Rest of the file
```

Set the following variables in your .env for activating pusher as a broadcastdriver with your credentials.

```bash
BROADCAST_DRIVER=pusher
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_ID=
```


## Creating the front-end scaffold

To get started quickly with authentication of users lets make use of the basic scaffold of Laravel.

```bash
php artisan make:auth
```

I assume that you use Laravel Valet or you can open the webapplication by opening the url: "http://laravelecho.dev/". Register yourself as a new user in the application.
If you are having problems with opening the application url, please check out my other blog posts that will cover starting with Valet or Homestead on OSX.

## Adding a new broadcasting channel

For this demo we will create a public channel called "messages". Open "routes/channels.php" in your favourite editor.

```php
// Add the messages public channel
Broadcast::channel('messages', function() {
    return true;
});
```

Now to activate these channels in this file you have to activate the BroadcastServiceProvider class in config/app.php file. You can find it in the heading of "Application Service Providers".

```php
// config.app

    //... snip
        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class, // Uncomment this row.
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        
    //... snip    
```

## Lets create an MessagePosted event

This event will broadcast all public data to all listeners on the channel, when the event is fired.
 
```bash
php artisan make:event MessagePosted
```

And set the contents of this file to:

```php
<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessagePosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $user;
    protected $message;

    public function __construct(User $user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    public function broadcastWith()
    {
        // This must always be an array. Since it will be parsed with json_encode()
        return [
            'user' => $this->user->name,
            'message' => $this->message,
        ];
    }

    public function broadcastAs()
    {
        return 'newMessage';
    }

    public function broadcastOn()
    {
        return new Channel('messages');
    }
}

```

On purpose the class vars are not public. The data that is broadcasted can be set/overridden within the method broadcastWith. In this case I choose what data is explicit broadcasted and not the complete User model.

With the broadcastAs method you can give a custom name for the broadcasted Eventname, the default name what is broadcasted is App\Events\{ClassName}. In this case App\Events\MessagePosted.

Now.. Keep that broadcastAs method in your mind with its returned string value.. ;-)

## Creating the MessageController and route for receiving message

This controller will process and dispatch the incoming message event. Run the following command in your terminal:

```bash
php artisan make:controller MessageController
```

And set the following contents to the file:

```php
<?php

namespace App\Http\Controllers;

use App\Events\MessagePosted;
use Auth;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function post(Request $request)
    {
        event(new MessagePosted(Auth::user(), $request->get('message')));
    }
}
```

Add the following line to your routes file to active this new class and methods.  
```php
Route::post('/message', 'MessageController@post');
```

## FrontEnd action

Now that all the legwork is done for the backend. Lets finish this app by altering the homepage after login.

Open the file "resource/assets/js/components/Example.vue" and set the contents to:

```html
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Example Chat</div>

                    <div class="panel-body">
                        <ul>
                            <li v-for="item in messages">@{{ item.message }} - <i>@{{ item.user }}</i></li>
                        </ul>

                        <input type="text" v-model="message"><br />
                        <button class="btn btn-success" @click="send()">Send!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                messages: [],
                message: '',
            }
        },

        methods: {
            send() {
                axios.post('/message', {message: this.message})
                    .then((response) => {
                        this.message = '';
                    });
            }
        },

        mounted() {
            Echo.channel('messages')
                .listen('.newMessage', (message) => {
                    this.messages.push(message);
                });
        }
    }
</script>
```

Update the content section of the file "resources/views/home.blade.php" to:

```php
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <example></example>
                </div>
            </div>
        </div>
    </div>
</div>
```

In your terminal run the command "gulp" to render and set the frontend code.

## The issue explained in detail

If you look into the JS code:
```html
Echo.channel('messages')
    .listen('.newMessage', (message) => {
        this.messages.push(message);
    });
```

You can see a dot "." before newMessage. Laravel sets the default namespace to "App\Events\" so if you overwrite the broadcastAs method in your Event Class you must prefix the listen command with that dot.
If the dot is not presented it will listen the App\Events\newMessage eventname in this particular case.

In my case I was forgetting the Dot in front of the name. This way my PusherData in my websockets tab was correctly showing the eventname stated in the php file. Laravel Echo listened tot the full namespaced eventname and therefor not processing the incoming data.
