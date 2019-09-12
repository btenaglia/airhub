<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */

Route::get('/', function() {
    return View::make('index');
});

/**
 * All the public api requests here
 */

Route::group(['prefix' => '/web'], function () { 
	  Route::get('eula', 
        ['as' => 'eula', 'uses' => 'WebController@eula']
    );
    Route::get('privacy', 
        ['as' => 'privacy', 'uses' => 'WebController@privacy']
    );
    Route::get(
        '/reservation/status','ReservationController@status'
    );
    Route::get(
        'payments/response',
        ['as' => 'payments.response', 'uses' => 'PaymentController@responseTransaccion']
    ); 
    Route::get(
        'payments/responseDeclined',
        ['as' => 'payments.response', 'uses' => 'PaymentController@responseTransaccionDeclined']
    ); 
    Route::get(
        'payments/getIframe',
        ['as' => 'payments.response', 'uses' => 'PaymentController@getIframe']
    ); 
   
});	
 
Route::group(['prefix' => 'api/v1/public'], function () {
    
    Route::post('accounts/login', 
        ['as' => 'account.login', 'uses' => 'AccountController@login']
    );
    
    Route::post('accounts/login-mobile', 
        ['as' => 'account.login-mobile', 'uses' => 'AccountController@loginMobile']
    );
    
    Route::post('accounts/loginfb', 
        ['as' => 'account.loginfb', 'uses' => 'AccountController@loginMobileFB']
    );
    
    Route::post('accounts/loginfb2', 
        ['as' => 'account.loginfb2', 'uses' => 'AccountController@loginMobileFB2']
    );
    
    Route::post('/accounts/create',
        ['as' => 'accounts.create', 'uses' => 'AccountController@createMobileUser']
    );
    Route::post('/accounts/recover',
        ['as' => 'accounts.recover', 'uses' => 'AccountController@recoverMobileUser']
    );
    Route::get('/accounts/testapi',
        ['as' => 'accounts.testapi', 'uses' => 'AccountController@testapiAM']
    );
    Route::get('/tests/test',
        ['as' => 'tests.test', 'uses' => 'TestController@test']
    );
    Route::post('/payments/status',
    ['as' => 'payment.status', 'uses' => 'PaymentController@updateStatusPayment']);
    Route::get('/places',
    ['as' => 'places.all', 'uses' => 'PlaceController@all']);
    Route::post(
        'contacts/',
        ['as' => 'payments.response', 'uses' => 'AccountController@ContactEmail']
    ); 
 
});

/**
 * All the private (token auth) api requests here
 */
Route::group(['prefix' => 'api/v1/private'], function() {

    Route::group(['before' => 'jwt-auth'], function() {
        
        /* @deprecated
         * Implemented in the client-side
         * Route::get('/logout',
            ['as' => 'account.logout', 'uses' => 'AccountController@logout']
        );*/
        
        Route::get(
            'accounts/current-user', 
            ['as' => 'account.current-user', 'uses' => 'AccountController@getCurrentUser']
        );
        
        /**
         * Users routes (backend users)
         */       
        Route::post(
            '/users/create',
            ['as' => 'users.create', 'uses' => 'AccountController@create']
        );
        Route::put(
            '/users/{id}/edit',
            ['as' => 'users.edit', 'uses' => 'AccountController@edit']
        );
        
        Route::post(
            '/musers/create',
            ['as' => 'musers.create', 'uses' => 'AccountController@createm']
        );
        Route::put(
            '/musers/{id}/edit',
            ['as' => 'musers.edit', 'uses' => 'AccountController@editm']
        );
        Route::get(
            '/musers',
            ['as' => 'users.all_mobile', 'uses' => 'AccountController@all_mobile']
        );
        
        
        /*REST API*/
        Route::put(
            '/users/modify',
            ['as' => 'users.edit', 'uses' => 'AccountController@modify']
        );
        
        /*Route::put(
            '/users/password_change',
            ['as' => 'users.password_change', 'uses' => 'AccountController@passwordChange']
        );*/
        
        Route::delete(
            '/users/{id}/destroy',
            ['as' => 'users.destroy', 'uses' => 'AccountController@destroy']
        );
        Route::get(
            '/users',
            ['as' => 'users.all', 'uses' => 'AccountController@all']
        );
        
        Route::get(
            '/users/{id}',
            ['as' => 'users.find', 'uses' => 'AccountController@find']
        );
        Route::get(
            '/users/sendpush/{id}',
            ['as' => 'users.sendpush', 'uses' => 'AccountController@SendPushtoUser']
        );
        
        /**
         * Account routes - This are the users of the mobile apps
         */
        Route::put(
            '/accounts/{id}/edit',
            ['as' => 'accounts.edit', 'uses' => 'AccountController@edit']
        );
        Route::delete(
            '/accounts/{id}/destroy',
            ['as' => 'accounts.destroy', 'uses' => 'AccountController@destroy']
        );
        Route::get(
            '/accounts',
            ['as' => 'accounts.all', 'uses' => 'AccountController@all']
        );
        Route::get(
            '/accounts/{id}',
            ['as' => 'accounts.find', 'uses' => 'AccountController@find']
        );
        
        /**
         * Places routes
         */
        Route::post(
            '/places/create',
            ['as' => 'places.create', 'uses' => 'PlaceController@create']
        );
        Route::put(
            '/places/{id}/edit',
            ['as' => 'places.edit', 'uses' => 'PlaceController@edit']
        );
        Route::delete(
            '/places/{id}/destroy',
            ['as' => 'places.destroy', 'uses' => 'PlaceController@destroy']
        );
        Route::get(
            '/places',
            ['as' => 'places.all', 'uses' => 'PlaceController@all']
        );
        Route::get(
            '/places/{id}',
            ['as' => 'places.find', 'uses' => 'PlaceController@find']
        );
        /**
         * members
         */
        Route::post(
            '/members/create',
            ['as' => 'members.create', 'uses' => 'MemberController@create']
        );
        Route::put(
            '/members/{id}/edit',
            ['as' => 'members.edit', 'uses' => 'MemberController@edit']
        );
        Route::delete(
            '/members/{id}/destroy',
            ['as' => 'members.destroy', 'uses' => 'MemberController@destroy']
        );
        Route::get(
            '/members',
            ['as' => 'members.all', 'uses' => 'MemberController@all']
        );
        Route::get(
            '/members/{id}',
            ['as' => 'members.find', 'uses' => 'MemberController@find']
        );
        Route::post(
            '/members/notification',
            ['as' => 'members.notification', 'uses' => 'MemberController@notification']
        );
        /**
         * Reservations
         */
        Route::post(
            '/reservation/create',
            ['as' => 'reservation.create', 'uses' => 'ReservationController@create']
        );
        Route::put(
            '/reservation/{id}/edit',
            ['as' => 'reservation.edit', 'uses' => 'ReservationController@edit']
        );
        Route::delete(
            '/reservation/{id}/destroy',
            ['as' => 'reservation.destroy', 'uses' => 'ReservationController@destroy']
        );
        Route::get(
            '/reservation',
            ['as' => 'reservation.all', 'uses' => 'ReservationController@all']
        );
        /**
         * Profiles routes
         */
        Route::post(
            '/profiles/create',
            ['as' => 'profiles.create', 'uses' => 'ProfileController@create']
        );
        Route::put(
            '/profiles/{id}/edit',
            ['as' => 'profiles.edit', 'uses' => 'ProfileController@edit']
        );
        Route::delete(
            '/profiles/{id}/destroy',
            ['as' => 'profiles.destroy', 'uses' => 'ProfileController@destroy']
        );
        Route::get(
            '/profiles',
            ['as' => 'profiles.all', 'uses' => 'ProfileController@all']
        );
        Route::get(
            '/profiles/{id}',
            ['as' => 'profiles.find', 'uses' => 'ProfileController@find']
        );
        
        /**
         * Setup routes
         */
        Route::put(
            '/setup/{id}/edit',
            ['as' => 'setup.edit', 'uses' => 'SetupController@edit']
        );
        Route::get(
            '/setup',
            ['as' => 'setup.all', 'uses' => 'SetupController@all']
        );
        Route::get(
            '/setup/{id}',
            ['as' => 'setup.find', 'uses' => 'SetupController@find']
        ); 
        
        /**
         * Config routes
         */
        Route::get(
            '/config/alerts',
            ['as' => 'config.alertsview', 'uses' => 'ConfigController@cview']
        );
        Route::post(
            '/config/alerts',
            ['as' => 'config.alertsmod', 'uses' => 'ConfigController@cedit']
        ); 
        
        /**
         * Planes routes
         */
        Route::post(
            '/planes/create',
            ['as' => 'planes.create', 'uses' => 'PlaneController@create']
        );
        Route::put(
            '/planes/{id}/edit',
            ['as' => 'planes.edit', 'uses' => 'PlaneController@edit']
        );
        Route::delete(
            '/planes/{id}/destroy',
            ['as' => 'planes.destroy', 'uses' => 'PlaneController@destroy']
        );
        Route::get(
            '/planes',
            ['as' => 'planes.all', 'uses' => 'PlaneController@all']
        );
        Route::get(
            '/planes/{id}',
            ['as' => 'planes.find', 'uses' => 'PlaneController@find']
        );
        
        /**
         * Flights routes
         */
        Route::post(
            '/flights/create',
            ['as' => 'flights.create', 'uses' => 'FlightController@create']
        );
        Route::put(
            '/flights/{id}/edit',
            ['as' => 'flights.edit', 'uses' => 'FlightController@edit']
        );
        Route::put(
            '/flights/{id}/set-plane',
            ['as' => 'flights.set-plane', 'uses' => 'FlightController@setPlane']
        );
        Route::delete(
            '/flights/{id}/destroy',
            ['as' => 'flights.destroy', 'uses' => 'FlightController@destroy']
        );
        Route::get(
            '/flights',
            ['as' => 'flights.all', 'uses' => 'FlightController@all']
        );
        Route::get(
            '/flights/test',
            ['as' => 'flights.test', 'uses' => 'FlightController@test']
        );
        Route::get(
            '/flights/future',
            ['as' => 'flights.future', 'uses' => 'FlightController@futureFlights']
        );
        Route::get(
            '/flights/passed',
            ['as' => 'flights.passed', 'uses' => 'FlightController@passedFlights']
        );
        Route::get(
            '/flights/{id}',
            ['as' => 'flights.find', 'uses' => 'FlightController@find']
        );
        Route::get(
            '/flights/{origin}/{destination}',
            ['as' => 'flights.flightsByPlaces', 'uses' => 'FlightController@flightsByPlaces']
        );
        Route::post(
            '/flights/{id}/approve',
            ['as' => 'flights.approve', 'uses' => 'FlightController@approve']
        );
        
        Route::post(
            '/flights/{id}/cancel',
            ['as' => 'flights.cancel', 'uses' => 'FlightController@cancel']
        );
        
        Route::get(
            '/allowed-flight-status',
            ['as' => 'flights.allowed-flight-status', 'uses' => 'FlightController@getAllowedStatus']
        );
        
        Route::get(
            '/created-flight-status',
            ['as' => 'flights.created-flight-status', 'uses' => 'FlightController@getCreatedStatus']
        );
        
        /**
         * Books routes
         */
        Route::post(
            '/bookings/create',
            ['as' => 'bookings.create', 'uses' => 'BookController@create']
        );
        Route::post(
            '/bookings/test',
            ['as' => 'bookings.test', 'uses' => 'BookController@testcc']
        );
        Route::get(
            '/bookings',
            ['as' => 'bookings.all', 'uses' => 'BookController@all']
        );
        Route::get(
            '/bookings/{id}',
            ['as' => 'bookings.find', 'uses' => 'BookController@find']
        );
        
        Route::get(
            '/bookings-by-flight/{id}',
            ['as' => 'bookings.find-by-flight', 'uses' => 'BookController@findByFlight']
        );
        
        Route::get(
            '/bookings-by-user',
            ['as' => 'bookings.find-by-user', 'uses' => 'BookController@findByUser']
        );
        Route::post(
            '/bookings/{id}/cancel',
            ['as' => 'bookings.cancel', 'uses' => 'BookController@cancel']
        );
        
        /**
         * Payments routes
         */
        Route::post(
            '/payments/getUrlPayment',
            ['as' => 'payments.getUrl', 'uses' => 'PaymentController@paymentPaya']
        ); 
        
        Route::get(
            '/payments/token',
            ['as' => 'payments.token', 'uses' => 'PaymentController@GetToken']
        ); 
        Route::get(
            '/payments',
            ['as' => 'payments.all', 'uses' => 'PaymentController@all']
        ); 
        Route::get(
            '/payments/ticket-cost',
            ['as' => 'payments.ticket-cost', 'uses' => 'PaymentController@getTicketCost']
        );
        Route::post(
            '/payments/ticket-cost',
            ['as' => 'payments.ticket-cost', 'uses' => 'PaymentController@getTicketCost2']
        );
        Route::post(
            '/payments/{id}/capture',
            ['as' => 'payments.find', 'uses' => 'PaymentController@capturePayment']
        );
        Route::post(
            'payments/reservationMobileCreate',
            ['as' => 'payments.response', 'uses' => 'PaymentController@reservationMobileCreate']
        ); 
        Route::get('/tests/testauth',
        ['as' => 'tests.testauth', 'uses' => 'TestController@testauth']
        );
    });
});