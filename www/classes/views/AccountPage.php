<?php

class AccountPage extends Page {

	// Properties
	private $existingPasswordError;
	private $newPasswordError;
	private $confirmPasswordError;
	private $passwordChangeMessage;
	private $userDeleteError;
	private $userEnableError;
	private $userEnableSuccess;
	private $disableUserMessage;
	private $profileImageError;
	
	private $firstName;
	private $firstNameError;
	private $lastName;
	private $lastNameError;
	private $jobTitle;
	private $jobTitleError;
	private $bio;
	private $bioError;
	private $staffSuccessMessage;
	private $staffErrorMessage;



	public function __construct($model) {
		parent::__construct($model);

		// If the user has submitted the password change form
		if( isset($_POST['existing-password']) ) {
			$this->processPasswordChange();
		}
		
		// Make sure the logged in user is an admin
		// if (isset($_SESSION['privilege']) && $_SESSION['privilege'] == 'admin' ) {
		

		// If the admin has submitted the delete user form
		if (isset($_POST['delete-button'])) {
			// Run the delete user function
			$this->model->deleteUser();			
		}
		if (isset($_POST['enable-account'])) {
			// Run the delete user function
			$this->model->processEnableAccount();			
		}

		// If the admin has submitted the staff form
		if (isset($_POST['new-staff-button'])) {
			$this->processAddStaff();
		}
	// }
	}
	
	public function contentHTML() {

		// Make sure the user is logged in
		// If not then offer them a login or registration link
		if( !isset($_SESSION['username']) ) {
			echo 'You need to be logged in';
			return;
		}


		include 'templates/accountpage.php';

		// If user is an admin
		if( $_SESSION['privilege'] == 'admin' ) {

			include 'templates/admincontrols.php';

		}

	}

	private function processPasswordChange() {

		// Make life easier
		$existingPass = $_POST['existing-password'];
		$newPass      = $_POST['new-password'];
		$confirmPass  = $_POST['confirm-password'];

		// Validate
		if( strlen($existingPass) == 0 ) {
			$this->existingPasswordError = 'Required';
		} elseif( !$this->model->checkPassword($existingPass) ) {
			$this->existingPasswordError = 'Incorrect password';
		}

		if( strlen($newPass) < 8 ) {
			$this->newPasswordError = 'Needs to be more than 8 characters';
		}

		if( strlen($confirmPass) < 8 ) {
			$this->confirmPasswordError = 'Needs to be more than 8 characters';
		} elseif( $confirmPass != $newPass ) {
			$this->confirmPasswordError = 'Does not match the new password';
		}

		// If no errors
		if( $this->existingPasswordError == '' && $this->newPasswordError == '' && $this->confirmPasswordError == '' ) {

			// Update the password
			$result = $this->model->updatePassword();

			// If updating the password was a success
			if( $result ) {
				$this->passwordChangeMessage = 'Successfully changed your password!';
			} else {
				$this->passwordChangeMessage = 'Something went wrong updating your password...';
			}

		}

	}

	public function processEnableAccount() {
		
		// Validation
		if (isset($_POST['username']) ) {
			$username = $_POST['username'];
		} else {
			$this->userEnableError = 'No username Selected';
		}
		if ($this->userEnableError == '') {
			$this->model->enableAccount($username);
			$this->userEnableSuccess = 'Successfully enabled the user';
		}

	}

	private function processAddStaff() {

		$this->firstName 	= trim($_POST['first-name']);
		$this->lastName 	= trim($_POST['last-name']);
		$this->jobTitle 	= trim($_POST['job-title']);
		$this->bio 			= trim($_POST['bio']);

		// Validate the form to make sure the user has provided all the appropriate fields
		if ( strlen($this->firstName) < 2 ) {
			$this->firstNameError = 'First name needs to be at least 2 characters.';
		} elseif (strlen($this->firstName > 20 )) {
			$this->firstNameError = 'First name is limited to 20 characters.';
		} elseif (!preg_match('/^[\w.\-\s]{2,20}$/', $this->firstName)) {
			$this->firstNameError = 'First Name is limited to letters, hyphens and fullstops.';
		}

		if ( strlen($this->lastName) < 2 ) {
			$this->lastNameError = 'Last name needs to be at least 2 characters.';
		} elseif (strlen($this->lastName > 20 )) {
			$this->lastNameError = 'Last name is limited to 20 characters.';
		} elseif (!preg_match('/^[\w.\-\s]{2,20}$/', $this->lastName)) {
			$this->lastNameError = 'Last Name can only include letters, hyphens & fullstops.';
		}

		if ( strlen($this->jobTitle == '') ) {
			$this->jobTitleError = 'Job Title is required';
		} elseif (strlen($this->jobTitle) > 30 ) {
			$this->jobTitleError = 'Job title is limited to 30 characters';
		} elseif (!preg_match('/^[\w\- \.]{2,30}$/', $this->jobTitle)) {
			$this->jobTitleError = 'Job Title can only include letters, hyphens & fullstops';
		}

		if ( strlen($this->bio > 200) ) {
			$this->bioError = 'Bio is limited to 200 characters. You have '.(strlen($this->bio) - 200);
		} elseif (!preg_match('/^[\w\s\-\.]{2,200}$/', $this->bio)) {
			$this->bioError = 'Bio can only include letters, hyphens & fullstops';
		}

		// Make life easier
		$file 		= $_FILES['profile-image'];
		$imageName 	= $file['name'];
		

		// if the user has not provided an image
		// first brackets['source']second brackets['What field we want']
		if( $imageName == '' ) {
			$this->profileImageError = 'Required';
		} elseif($this->firstNameError == '' && $this->lastNameError == '' && $this->jobTitleError == '' && $this->bioError == '') {
			
			// require the image upload class
			require 'vendor/ImageUploader.php';

			// Instantiate the class
			$imageUploader = new ImageUploader();

			// Mkae new filename based oon the staff members name
			$fileName = $this->firstName.'-'.$this->lastName;

			// Upload the image and make sure all went well
			$result = $imageUploader->upload('profile-image','img/staff/original/',$fileName);

			// If something went wrong
			if (!$result) {
				$this->profileImageError = $imageUploader->errorMessage;
			} else {
				$newImage = $imageUploader->getImageName();
				$imageUploader->resizeImage('img/staff/original/'.$newImage, 320, 'img/staff/thumbnails/',$newImage);
			}

				// If there are no errors then insert a new staff member
			if ($this->profileImageError == '') {
				$result = $this->model->addNewStaff($newImage);

				// if success
				if ($result) {
					$this->staffSuccessMessage = 'Success';
				} else {
					$this->staffErrorMessage = 'Something went wrong in the Database';
				}
			}
		}

		
	}

}









