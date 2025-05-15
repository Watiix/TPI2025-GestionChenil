<?php

use Lucancstr\GestionChenil\Controllers\HomeController;
use Lucancstr\GestionChenil\Controllers\AuthController;
use Lucancstr\GestionChenil\Controllers\AnimalController;
use Lucancstr\GestionChenil\Controllers\UtilisateurController;
use Lucancstr\GestionChenil\Controllers\ReservationController;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/', [HomeController::class, 'showHomePage']);
$app->get('/register', [HomeController::class, 'showRegisterPage']);
$app->get('/login', [HomeController::class, 'showLoginPage']);

$app->post('/register-post', [AuthController::class, 'createAccount']);
$app->post('/login-post', [AuthController::class, 'login']);
$app->get('/logout', [AuthController::class, 'logout']);

$app->get('/animaux', [AnimalController::class, 'getAnimaux']);
$app->post('/animal-post', [AnimalController::class, 'addAnimal']);
$app->get('/animal-form', [AnimalController::class, 'showAnimalFormPage']);
$app->get('/animal-delete/{id:[0-9]+}', [AnimalController::class, 'deleteAnimal']);
$app->get('/animal-edit/{id:[0-9]+}', [AnimalController::class, 'showEditForm']);
$app->post('/animal-update/{id:[0-9]+}', [AnimalController::class, 'updateAnimal']);

$app->get('/utilisateurs', [UtilisateurController::class, 'getUsers']);
$app->get('/utilisateur-accepted/{id:[0-9]+}', [UtilisateurController::class, 'acceptUser']);
$app->get('/utilisateur-refused/{id:[0-9]+}', [UtilisateurController::class, 'refusedUser']);
$app->get('/utilisateur-delete/{id:[0-9]+}', [UtilisateurController::class, 'refusedUser']);
$app->get('/utilisateur-showForm', [UtilisateurController::class, 'showUserForm']);
$app->get('/utilisateur-edit/{id:[0-9]+}', [UtilisateurController::class, 'showEditForm']);
$app->post('/utilisateur-update/{id:[0-9]+}', [UtilisateurController::class, 'editUser']);
$app->post('/utilisateur-add', [UtilisateurController::class, 'addUtilisateur']);

$app->get('/reservations', [ReservationController::class, 'getReservation']);
$app->get('/reservation-accepted/{id:[0-9]+}', [ReservationController::class, 'acceptReservation']);
$app->get('/reservation-refused/{id:[0-9]+}', [ReservationController::class, 'refusedReservation']);
$app->get('/reservation-delete/{id:[0-9]+}', [ReservationController::class, 'deleteReservation']);
$app->get('/reservation-showForm', [ReservationController::class, 'showReservationForm']);
$app->post('/reservation-add', [ReservationController::class, 'addReservation']);
$app->get('/reservation-edit/{id:[0-9]+}', [ReservationController::class, 'showEditForm']);
$app->post('/reservation-update/{id:[0-9]+}', [ReservationController::class, 'editReservation']);