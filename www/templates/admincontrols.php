<div class="row">
	<div class="columns">
	<h2>Enable / Disable accounts</h2>
	</div>
		<div class="columns medium-6">
		<h3>Disable an account</h3>
			<form method="POST" action="index.php?page=account">
			<label>Users: </label>
			<select name="user-list">
				<?php

					// Use the model to get all accounts
					$result = $this->model->getAllUsernames();

					// Loop through the result and display all the usernames
					while( $row = $result->fetch_assoc() ) {

						// Show all users that are enabled
						if ($row['Active'] == 'disabled') {
							continue;
						}

						echo '<option>'.$row['Username'].'</option>';
					}

				?>
			</select>
			<input type="submit" value="Disable User" class="button alert small" name="delete-button">
			<?php if($this->userDeleteError != '') : ?>
			<small class='error'><?php echo $this->userDeleteError; ?></small>
			<?php endif ?>
			<?php if ($this->disableUserMessage != '') : ?> 
			<small class"alert-box success"><?php echo $this->disableUserMessage; ?></small>
			<?php endif;?>
		</form>
	</div>
	<div class="columns medium-6">
		<h3>Enable an account</h3>
		<form action="index.php?page=account" method="POST">
			<label>Users: </label>
			<select name="username">
				<?php 
					
					$result = $this->model->getAllUsernames();

					while ($row = $result->fetch_assoc()) {
		
					if ($row['active'] == 'enabled') {
						continue;
					}

					echo '<option>'.$row['Username'].'</option>';
				}
				?>
			</select>
		<input type="submit" class="tiny button" value="Enable this account" name="enable-account">
		<?php if( $this->userEnableError != '' ) : ?>
		<small class="error"><?php echo $this->userEnableError; ?></small>
		<?php endif; ?>
		<?php if( $this->userEnableSuccess != '' ) : ?>
		<small class="alert-box success"><?php echo $this->userEnableSuccess; ?></small>
		<?php endif; ?>
		</form>
	</div>
</div>

<div class="row">
	<div class="columns">
		<h2>Add new staff member</h2>
		<form action="index.php?page=account" method="POST" enctype="multipart/form-data">
			<div class="row">
				<div class="columns medium-4">
					<label for="first-name">First Name: </label>
					<input type="text" name="first-name" id="first-name" placeholder="Anthony" value="<?php echo $this->firstName; ?>">
					<?php errorMessage($this->firstNameError); ?>
				</div>
				<div class="columns medium-4">
					<label for="last-name">Last Name: </label>
					<input type="text" name="last-name" id="last-name" placeholder="Biron" value="<?php echo $this->lastName; ?>">
					<?php errorMessage($this->firstNameError); ?>
				</div>
				<div class="columns medium-4">
					<label for="job-title">Job Title</label>
					<input type="text" name="job-title" id="job-title" placeholder="Da Boss" value="<?php echo $this->jobTitle; ?>">
					<?php errorMessage($this->firstNameError); ?>
				</div>
				<div class="cloumns medium-6">
				<label for="bio">Bio: </label>
					<textarea name="bio" id="bio" rows="4" cols="20"><?php echo $this->bio; ?></textarea>
					<?php errorMessage($this->bioError); ?>
				</div>
				<div class="columns medium-6">
				<label for="profile-image">Profile Image: </label>
					<input type="hidden" name="MAX_FILE_SIZE" value="5000000">
					<input type="file" class="button small" name="profile-image" id="profile-image">
					<?php errorMessage($this->profileImageError); ?>
				</div>
				<div>
					<input type="submit" class="button" value="Add New Staff Member" name="new-staff-button">
					<?php errorMessage($this->staffErrorMessage); 
						
						// If there is a message to display
						$this->foundationAlert($this->staffSuccessMessage, 'success');
					
					?>
				</div>
			</div>			
		</form>
	</div>
</div>