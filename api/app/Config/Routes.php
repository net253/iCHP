<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

//! 1. Authentication
//! 1.1 Login
$routes->post('/auth/login', 'AuthController::login');
//! 1.2 Change Password
// $routes->post('/auth/change-password', 'AuthController::changePassword', ['filter' => 'authFilter']);
$routes->post('/auth/change-password', 'AuthController::changePassword');
//! 1.3 Check Authentication
// $routes->post('/auth/check-auth', 'AuthController::checkAuth', ['filter' => 'authFilter']);
$routes->post('/auth/check-auth', 'AuthController::checkAuth');
$routes->post('/auth/forgot-password', 'AuthController::forgotPassword');


// api RequestController.php  
$routes->post('/Request/create-requestform', 'RequestController::createRequestForm');
$routes->post('/Request/satisfy-score', 'RequestController::SatisfyScore');
$routes->post('/Request/approve-status', 'RequestController::approveStatus');
$routes->post('/Request/operator-status', 'RequestController::operatorStatus');
$routes->post('/Request/software-status', 'RequestController::softwareStatus');
$routes->get('/Request/request-list', 'RequestController::requestList');
$routes->get('/Request/request-detail', 'RequestController::requestDetail');
$routes->get('/Request/project-name', 'RequestController::projectName');
$routes->get('/Request/snccompany-list', 'RequestController::sncCompanyList');
$routes->get('/Request/managername-list', 'RequestController::managerNameList');



/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
