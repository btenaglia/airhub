<?php
namespace App\Services;

use Illuminate\Support\ServiceProvider;

/**
 * Configure the depenency injection for the services.
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class IoCServiceProvider extends ServiceProvider {
    
    public function register() {
        $this->app->bind('AccountService', function() {
            $service = new AccountService();
            return $service;
        });
        
        $this->app->bind('PlaceService', function() {
            $service = new PlaceService();
            return $service;
        });
        
        $this->app->bind('PlaneService', function() {
            $service = new PlaneService();
            return $service;
        });
        
        $this->app->bind('ProfileService', function() {
            $service = new ProfileService();
            return $service;
        });
        
        $this->app->bind('FlightService', function() {
            $service = new FlightService();
            return $service;
        });
        
        $this->app->bind('BookService', function() {
            $service = new BookService();
            return $service;
        });
        
        $this->app->bind('PaymentService', function() {
            $service = new PaymentService();
            return $service;
        });
        
        $this->app->bind('MailService', function() {
            $service = new MailService();
            return $service;
        });
        
        $this->app->bind('MconfigService', function() {
            $service = new MconfigService();
            return $service;
        });
        
        $this->app->bind('MconfigusersService', function() {
            $service = new MconfigusersService();
            return $service;
        });
        
        $this->app->bind('SetupService', function() {
            $service = new SetupService();
            return $service;
        });
        
        $this->app->bind('PushService', function() {
            $service = new PushService();
            return $service;
        });
        
    }
}
