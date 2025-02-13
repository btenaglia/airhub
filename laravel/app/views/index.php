<!doctype html>
<!--[if lt IE 8]>         <html class="no-js lt-ie8"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <title>Air Hub</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

        <!-- Needs images, font... therefore can not be part of main.css -->
        <link rel="stylesheet" href="styles/loader.css">
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,100,500,700,300,300italic,500italic|Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="bower_components/angular-material/angular-material.min.css">        
        
        <link rel="stylesheet" href="styles/main.css">
        <link rel="stylesheet" href="styles/custom.css">
        <!-- end Needs images -->

    </head>
    <body data-ng-app="app"
          id="app"
          class="app"
          data-custom-page 
          data-ng-controller="AppCtrl"
          data-ng-class=" { 'layout-boxed': main.layout === 'boxed', 
                            'nav-collapsed-min': main.menu === 'collapsed'
          } ">
        <!--[if lt IE 9]>
            <div class="lt-ie9-bg">
                <p class="browsehappy">You are using an <strong>outdated</strong> browser.</p>
                <p>Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
            </div>
        <![endif]-->

        <div id="loader-container"></div>

        <header data-ng-include=" 'views/layout/header.html' "
                 id="header"
                 class="header-container "
                 data-ng-controller="HeaderController"
                 data-ng-class="{ 'header-fixed': main.fixedHeader,
                                  'bg-white': ['11','12','13','14','15','16','21'].indexOf(main.skin) >= 0,
                                  'bg-dark': main.skin === '31',
                                  'bg-primary': ['22','32'].indexOf(main.skin) >= 0,
                                  'bg-success': ['23','33'].indexOf(main.skin) >= 0,
                                  'bg-info': ['24','34'].indexOf(main.skin) >= 0,
                                  'bg-warning': ['25','35'].indexOf(main.skin) >= 0,
                                  'bg-danger': ['26','36'].indexOf(main.skin) >= 0
                 }"></header>

        <div class="main-container"
             data-ng-class="{ 'app-nav-horizontal': main.menu === 'horizontal' }">
            <aside data-ng-include=" 'views/layout/sidebar.html' "
                   id="nav-container"
                   class="nav-container"  
                   data-ng-class="{ 'nav-fixed': main.fixedSidebar,
                                    'nav-horizontal': main.menu === 'horizontal',
                                    'nav-vertical': main.menu === 'vertical',
                                    'bg-white': ['31','32','33','34','35','36'].indexOf(main.skin) >= 0,
                                    'bg-dark': ['31','32','33','34','35','36'].indexOf(main.skin) < 0
                   }">
            </aside>

            <div id="content" class="content-container">
                <section data-ui-view
                         class="view-container {{main.pageTransition.class}}"></section>
            </div>
        </div>

        <!-- build:js scripts/vendor.js -->
        <script src="bower_components/jquery/dist/jquery.min.js"></script>
        <script src="bower_components/angular/angular.min.js"></script>
        <script src="bower_components/angular-animate/angular-animate.min.js"></script>
        <script src="bower_components/angular-aria/angular-aria.min.js"></script>
        <script src="bower_components/angular-messages/angular-messages.min.js"></script>
        <script src="bower_components/angular-ui-router/release/angular-ui-router.min.js"></script>
        <!-- endbuild -->


        <!-- build:js scripts/ui.js -->
        <script src="bower_components/angular-material/angular-material.min.js"></script>
        <script src="bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
        <script src="bower_components/jquery-steps/build/jquery.steps.min.js"></script>
        <script src="bower_components/jquery.slimscroll/jquery.slimscroll.min.js"></script>

        <script src="bower_components/angular-ui-tree/dist/angular-ui-tree.min.js"></script>
        <script src="bower_components/ngmap/build/scripts/ng-map.min.js"></script>
        <script src="bower_components/angular-scroll/angular-scroll.min.js"></script>
        <script src="bower_components/angular-validation-match/dist/angular-validation-match.min.js"></script>

        <script src="bower_components/textAngular/dist/textAngular-rangy.min.js"></script>
        <script src="bower_components/textAngular/dist/textAngular.min.js"></script>
        <script src="bower_components/textAngular/dist/textAngular-sanitize.min.js"></script>

        <script src="bower_components/angular-translate/angular-translate.min.js"></script>
        <script src="bower_components/angular-translate-loader-static-files/angular-translate-loader-static-files.min.js"></script>
        <!-- endbuild -->


        <!-- build:js scripts/app.js -->
        <!-- inject:js -->
        <script src="app/app.module.js"></script>
<!--
        <script src="app/core/core.module.js"></script>
        <script src="app/form/form.module.js"></script>
        <script src="app/form/formValidation.module.js"></script>
        <script src="app/layout/layout.module.js"></script>
-->
<!--
        <script src="app/page/page.module.js"></script>
        <script src="app/table/table.module.js"></script>
        <script src="app/ui/ui.module.js"></script>
-->
        <script src="app/core/app.config.js"></script>
        <script src="app/core/app.controller.js"></script>
        <script src="app/core/config.route.js"></script>
<!--
        <script src="app/dashboard/dashboard.controller.js"></script>
        <script src="app/form/form.controller.js"></script>
        <script src="app/form/form.directive.js"></script>
        <script src="app/form/formValidation.controller.js"></script>
        <script src="app/table/table.controller.js"></script>
-->

        <script src="app/layout/layout.diretive.js"></script>
        <script src="app/layout/loader.js"></script>
        <script src="app/layout/sidebar.directive.js"></script>
        
        <script src="app/page/page.controller.js"></script>
        <script src="app/page/page.directive.js"></script>
        
        <script src="app/ui/material.controller.js"></script>
        <script src="app/ui/ui.controller.js"></script>
        <script src="app/ui/ui.directive.js"></script>
        
        <script src="app/controllers/Notifications.js"></script>
        <script src="app/controllers/Accounts.js"></script>
        <script src="app/controllers/Auth.js"></script>
        <script src="app/controllers/DynamicTable.js"></script>
        <script src="app/controllers/Form.js"></script>
        <script src="app/controllers/Header.js"></script>
        <script src="app/controllers/Home.js"></script>
        <script src="app/controllers/Places.js"></script>
        <script src="app/controllers/Members.js"></script>
        <script src="app/controllers/Profiles.js"></script>
        <script src="app/controllers/Planes.js"></script>
        <script src="app/controllers/Flights.js"></script>
        <script src="app/controllers/Users.js"></script>
        <script src="app/controllers/Musers.js"></script>
        <script src="app/controllers/Bookings.js"></script>
        <script src="app/controllers/Payments.js"></script>
        <script src="app/controllers/Reservations.js"></script>
        <script src="app/controllers/Setup.js"></script>
        <script src="app/config/HttpConfig.js"></script>
        <!-- endinject -->
        <!-- build:js scripts/app.js -->
    </body>
</html>
