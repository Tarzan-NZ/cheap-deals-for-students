<?php

class ImageUploader {

	// Properties
	private $imageName;
	private $imageType;
	private $imageSize;
	private $imageError;
	private $imageTemp;
	private $inputName;
	private $destination;
	public $errorMessage;
	private $imageTypes = ['image/jpeg', 'image/gif','image/png'];

	// Function to sned back the name of the image
	public function getImageName(){ return $this->imageName; }

	// Methods(functions)
	public function upload($inputName, $destination, $newFileName='', $newWidth=0) {
	
		// Extract the information about the image
		$this->imageName 	= $_FILES[$inputName]['name'];
		$this->imageType 	= $_FILES[$inputName]['type'];
		$this->imageSize 	= $_FILES[$inputName]['size'];
		$this->imageError 	= $_FILES[$inputName]['error'];
		$this->imageTemp 	= $_FILES[$inputName]['tmp_name'];


		$this->inputName 	= $inputName;
		$this->destination 	= $destination;

		// Show max files size
		if ($_POST['MAX_FILE_SIZE'] < 1000 ) {
			$maxSize = $_POST['MAX_FILE_SIZE'].' Bytes';
		} elseif ($_POST['MAX_FILE_SIZE'] < 1000000) {
			$maxSize = ($_POST['MAX_FILE_SIZE'] / 1000).' Kilobytes';
		} else {
			$maxSize = ($_POST['MAX_FILE_SIZE'] / 1000000).' Megabytes';
		}
		
		// Check for errors
		switch ($this->imageError) {
			case 1:	$this->errorMessage = 'Image too large for the server'; break;
			case 2:	$this->errorMessage = 'Image size exceeds form filesize limit';	break;
			case 3:	$this->errorMessage = 'Image only partially uploaded'; break;
			case 4:	$this->errorMessage = 'Image failed to load / no image selected'; break;
		}

		// If an error occured
		if ($this->errorMessage != '') {
			return false;
		}
		
		// File type
		if (!in_array($this->imageType, $this->imageTypes )) {
			$this->errorMessage = 'Invalid file type';
			return false;
		}

		// Generate a UID to be used on the file name
		$unique = uniqid('', true);

		// If anew file name has been provided
		if ($newFileName == '') {
			$this->imageName = $uniqid.$this->imageName;
		} else {
			// Get the file extension
			$fileExtension = pathinfo($this->imageName, PATHINFO_EXTENSION);
			$this->imageName = $unique.$newFileName.'.'.$fileExtension;
		}


		// Move the image from the temp location to the final destination
		@move_uploaded_file($this->imageTemp, $this->destination.$this->imageName);

		// If the file did not make it to the destination

		if (!file_exists( $this->destination.$this->imageName)) {
			$this->errorMessage = 'Could not move file to final destination. Permission issue?';
			return false;
		}

		// Everything is done 
		return true;

	}

	public function resizeImage($originalFileLocation, $newWidth, $destination, $imageName) {
		
		$mime = mime_content_type($originalFileLocation);

		// Get the mime type
		switch ($mime) {

			case 'image/jpeg':
				$originalImage = imagecreatefromjpeg($originalFileLocation);
			break;

			case 'image/png':
				$originalImage = imagecreatefrompng($originalFileLocation);
			break;

			case 'image/gif';
				$originalImage = imagecreatefromgif($originalFileLocation);
			break;
			
			default:
				die('Not an image !');
			break;
		}

		$dimensions = getimagesize($originalFileLocation);
	
		$originalWidth 	= $dimensions[0];
		$originalHeight = $dimensions[1];

		// Calculate the new height
		$newHeight = ($originalHeight / $originalWidth) * $newWidth;

		// Create a bran new image
		$newImage = imagecreatetruecolor($newWidth, $newHeight);

		// Copy the original image data onto this new smaller image
		imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

		switch ($mime) {
			case 'image/jpeg':
				imagejpeg($newImage, $destination.$imageName, 80);
			break;

			case 'image/png':
				imagepng($newImage, $destination.$imageName, 6);	
			break;

			case 'image/gif':
				imagegif($newImage, $destination.$imageName);	
			break;

		}

		// Delete any trace of the image from the server memory
		imagedestroy($originalImage);
		imagedestroy($newImage);

	}


}