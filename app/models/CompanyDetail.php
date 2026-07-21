<?php 
defined('ROOTPATH') OR exit('Access Denied!');

/**
 * The CompanyDetail Model Class
 */



class CompanyDetail
{
	
	use Model;

	protected $table = 'company_details';
	

	protected $allowedColumns = [
		'image',
		'name',
		'about',
		'email',
		'phone',
        'address',
        'updated_by',
        'date_updated',
	];

	public function validate(array $files_data, array $post_data, int|string|null $id = null): bool
	{

		$this->errors = [];
		// Allowed File types
        $allowed_types = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp' 
        ];

        // Image validation - Check inside the file 
        if(empty($files_data['image']['tmp_name']))
        {
            $this->errors['image'] = 'An image is required!';
        } else 
        if(!isset($files_data['image']['type']) || !in_array($files_data['image']['type'], $allowed_types))
        {
            $this->errors['image'] = 'Invalid Image File Type. Only types: ' . implode(', ', $allowed_types) . ' allowed!';
        } else 
		if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
			$this->errors['image'] = 'File upload error: ' . $_FILES['image']['error'];
		} 

		// Other inputs validation
        if(empty($post_data['name']))
		{
			$this->errors['name'] = "Company name is required";
		}
        
		if(!filter_var($post_data['email'],FILTER_VALIDATE_EMAIL))
		{
			$this->errors['email'] = "Email is not valid";
		} else 
		// Will come back and edit if it does not work out
		if($this->first(['email' => $post_data['email']],['id' => $id]) )
		{
			$this->errors['email'] = "Email is already in use!";
		}
		if(empty($post_data['address']))
		{
			$this->errors['address'] = "Company address is required";
		}

        if(empty($post_data['phone']))
		{
			$this->errors['phone'] = "Company phone is required";
		}
		
		if(empty($this->errors))
		{
			return true;
		}

		return false;
	}
}
