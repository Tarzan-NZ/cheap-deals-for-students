<div class="row">
	<div class="columns">
		<h1>Our Staff</h1>
	</div>
	<?php 

		// Get all staff members
		$allStaff = $this->model->getAllStaffMembers();

		// Loop through all the staff members
		while ($row = $allStaff->fetch_assoc() ) : ?>
			<div class="columns medium-4 large-3">
				<img src="img/staff/thumbnails/<?php echo $row['ProfileImage']; ?>">
				<h2><?php echo $row['FirstName'].' '.$row['LastName']; ?></h2>
				<p><?php echo $row['Bio']; ?></p>
			</div>
		<?php endwhile; ?>
	</div>