<?php

class AboutModel extends Model {

	public function getAllStaffMembers() {
		return $this->dbc->query("SELECT FirstName,LastName,ProfileImage,Bio,Job FROM Staff");
		
	}
	
}