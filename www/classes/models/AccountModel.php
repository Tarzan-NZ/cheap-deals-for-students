<?php

class AccountModel extends Model {

	public function getAllUsernames() {

		return $this->dbc->query( "SELECT Username,Active,Privilege FROM users WHERE Privilege = 'user' " );

	}

	public function checkPassword( $password ) {

		// Get the username of the person who is logged in
		$username = $_SESSION['username'];

		// Get the password of the account that is logged in
		$result = $this->dbc->query("SELECT Password FROM users WHERE Username = '$username'");

		// Convert into an associative array
		$data = $result->fetch_assoc();

		// Need the password compat library
		require 'vendor/password.php';

		// Compare the current password against user existing password
		if( password_verify($password, $data['Password']) ) {
			return true;
		} else {
			return false;
		}

	}

	public function updatePassword() {

		// Get the username of the person logged in
		$username = $_SESSION['username'];

		// Hash the new password
		require 'vendor/password.php';
		$hashedPassword = password_hash($_POST['new-password'], PASSWORD_BCRYPT);

		// Prepare UPDATE SQL
		$sql = "UPDATE users SET Password = '$hashedPassword' WHERE Username = '$username'";

		// Run the SQL
		$this->dbc->query($sql);

		// Ensure the password update worked
		if( $this->dbc->affected_rows != 0 ) {
			return true;
		} else {
			return false;
		}
	}

	public function deleteUser() {
		
		// Grab the user currently selected in the dropdown menu
		$selecteduser = $_POST['user-list'];

		// // Filter
		// $username = $this->dbc->real_escape_string($username);

		// Prepare the sql to delete the selected user
		$sql = "UPDATE users SET Active = 'disabled' WHERE Username = '$selecteduser' ";

		// Run the SQL
		$this->dbc->query( $sql );

		$disableUserMessage = 'User disabled !';

	}

	public function addNewStaff($imageName){
		
		// Extract the data from the form and filter it
		$firstName 	= $this->dbc->real_escape_string($_POST['first-name']);
		$lastName 	= $this->dbc->real_escape_string($_POST['last-name']);
		$jobTitle 	= $this->dbc->real_escape_string($_POST['job-title']);
		$bio 		= $this->dbc->real_escape_string($_POST['bio']);
		$image 		= $this->dbc->real_escape_string($imageName);

		// Delete once image upload is working
		// $image = 'http://placehold.it/320x180';

		// Prep SQL query
		$sql = "INSERT INTO Staff VALUES (NULL, '$firstName', '$lastName', '$image','$bio','$jobTitle')";

		// Run the query
		$this->dbc->query($sql);

		// Make sure the insert worked
		if ($this->dbc->affected_rows > 0 ) {
			return true; // Success
		}
	}





}






