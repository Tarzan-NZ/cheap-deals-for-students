<?php

class RegistrationModel extends Model {

	public function checkUsernameExists( $username ) {

		// Filter the username just in case it has malicious code
		$username = $this->dbc->real_escape_string( $username );

		// Prepare SQL
		$sql = "SELECT Username FROM users WHERE Username = '$username'";

		// Run the query
		$result = $this->dbc->query( $sql );

		// If there is a result
		if( $result->num_rows > 0 ) {

			// Account with that username exists
			return true;

		} else {

			// Account with that username does NOT exist
			return false;

		}

	}

	public function checkEmailExists( $email ) {

		// Filter the email address to make the SQL safe
		$email = $this->dbc->real_escape_string( $email );

		// Prepare SQL
		$sql = "SELECT Email FROM users WHERE Email = '$email'  ";

		// Run the SQL
		$result = $this->dbc->query( $sql );

		// If there is a result from the database
		return $result->num_rows ? true : false;

		// if( $result->num_rows ) {
		// 	// Email in use
		// 	return true;
		// } else {
		// 	// Email NOT in use
		// 	return false;
		// }

	}

	public function registerNewAccount( $username, $email, $password ) {

		// Filter the data
		$username = $this->dbc->real_escape_string($username);
		$email    = $this->dbc->real_escape_string($email);

		// Hash the password
		require 'vendor/password.php';

		$hashedPassword = password_hash( $password, PASSWORD_BCRYPT );

		// Prepare SQL for insert
		$sql = "INSERT INTO users 
				VALUES (NULL, '$username', '$email', '$hashedPassword', 'user', CURRENT_TIMESTAMP, 'enabled')";

		// Run the SQL
		$this->dbc->query($sql);

		// Validate the account creation

		// Log user in by saving their details into the session
		$_SESSION['username']  = $username;
		$_SESSION['privilege'] = 'user';

	}




}






