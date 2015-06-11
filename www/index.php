<?php

// Determine what page the user wants
$_GET['page'] = isset($_GET['page']) ? $_GET['page'] : 'home';

// Require the page class
require 'classes/Page.php';

// Require the Model class
require 'classes/models/Model.php';

// Switch based on requested page
switch( $_GET['page'] ) {

	// Home
	case 'home':
		require 'classes/models/HomeModel.php';
		require 'classes/HomePage.php';

		$model = new HomeModel();
		$page = new HomePage( $model );
	break;

	// About
	case 'about':
		require 'classes/models/AboutModel.php';
		require 'classes/views/AboutPage.php';

		$model = new AboutModel();
		$page = new AboutPage( $model );
	break;

	case 'contact':
		require 'classes/models/ContactModel.php';
		require 'classes/views/ContactPage.php';

		$model = new ContactModel();
		$page = new ContactPage( $model );
	break;

	// 404
	default:
		require 'classes/models/Error404Model.php';
		require 'classes/Error404Page.php';
		$model = new Error404Model();
		$page = new Error404Page( $model );
	break;
}

// Load the content
$page->buildHTML();






