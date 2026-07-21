<?php

/**
 * PostController class 
 */

defined('ROOTPATH') or exit('Access Denied!');

class PostController
{
	use Controller;

	public function __construct()
	{
		$user = new User();

		// Check if current user is logged in 
		if (!$user->logged_in())
			redirect('auth/login');
	}

	public function index(?string $id = null): void
	{
		$post = new Post();
		$data['posts'] = $post->postsWithcats();

		// Page title in view page
		$data['page_title'] = 'All Blog Posts';
		// Display posts view page
		$this->view('admin/posts/posts', $data);
	}

	public function create(?string $id = null): void
	{
		$user = new User();
		$post = new Post();
		$category = new Category();

		// Create blog Folder
		$folder = 'uploads/blog/';
		if (!file_exists($folder)) {
			mkdir($folder, 0777, true);
		}

		$data['posts'] = $post->postsWithcats();
		$data['categories'] = $category->findAll();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			if ($_FILES['image']['error'] == UPLOAD_ERR_OK && $post->validate($_FILES, $_POST)) {

				$destination = $folder . time() . '_' . $_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'], $destination);

				$_POST['image'] = $destination;
				$post->insert($_POST);
				// Success Notification
				Util::setFlash('post_create_success', 'Blog Post Created Successfully!!');

				redirect('admin/posts');
			}
		}

		$preselect_cat_id = $id ?? null;
		$data['preselect_cat_id'] = $preselect_cat_id;

		// Display validation errors (if any)
		$data['errors'] = $post->errors;

		// Page title in "view" page
		$data['page_title'] = 'Create New Post';

		// Return posts view
		$this->view('admin/posts/post-create', $data);
	}

	public function createCat(?string $id = null): void
	{
		$user = new User();
		$category = new Category();

		// Check if current user is looged in 
		if (!$user->logged_in())
			redirect('auth/login');

		$data['categories'] = $category->findAll();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			
			if ($category->validate($_POST)) {

				// Insert New Category into DB
				$category->insert($_POST);
				// Success Notification
				Util::setFlash('category_create_success', 'Post Category Created Successfully!!');

				redirect('admin/post/create/' . $_POST['sn']);
			}
		}


		// Page title in "view" page
		$data['page_title'] = 'Create New Category';

		// Return category view
		$this->view('admin/posts/create-category', $data);
	}

	public function edit(?string $id = null): void
	{
		$post = new Post();
		$category = new Category();

		$data['categories'] = $category->findAll();
		// Get the specific post
		$data['row'] = $post->first(['post_id' => $id]);
		// Execute only when edit button has been clicked
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// Check Input validation 
			if ($post->validate($_FILES, $_POST, $id)) {
				$folder = '';
				$destination = $folder . time() . '_' . $_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'], $destination);
				// Add image to the Post Array
				$_POST['image'] = $destination;
				// Check the CSRF
				if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
					die('Invalid CSRF Token!');
				} else {
					// Execute the update
					$post->update($id, $_POST, 'post_id');

					if (file_exists($data['row']->image))
						unlink($data['row']->image);

					Util::setFlash('post_update_success', 'Post updated successfully!!');
					redirect('admin/posts');
				}
			}
		}

		// Page title in "view" page
		$data['page_title'] = 'Edit Blog Post';

		// Return posts view
		$this->view('admin/posts/post-edit', $data);
	}
	public function delete(?string $id = null): void
	{
		$post = new Post();
		$category = new Category();
		$delItem = new DeletedItem();

		$data['categories'] = $category->findAll();
		
		// Get the specific post
		$data['row'] = $post->first(['post_id' => $id]);
		// Execute only when edit button has been clicked
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
				die('Invalid CSRF Token!');
			} else {
				$post->delete($id, 'post_id');
				$delItem->insert($_POST);

				if (file_exists($data['row']->image))
					unlink($data['row']->image);

				Util::setFlash('post_delete_success', 'Post deleted successfully!!');
				redirect('admin/posts');
			}
		}

		// Page title in "view" page
		$data['page_title'] = 'Delete Blog Post';

		// Return posts view
		$this->view('admin/posts/post-delete', $data);
	}
	public function post_view(?string $id = null): void
	{
		$post = new Post();
		
		// Get the specific post
		$data['row'] = $post->first(['post_id' => $id]);
		
		// Display Single post (tables joined)
		$data['single_post'] = $post->singlePost($id);

		// Page title in "view" page
		$data['page_title'] = 'Single Post View';

		// Return posts view
		$this->view('admin/posts/post-view', $data);
	}

	public function categories(): void
	{
		$category = new Category();
		$data['categories'] = $category->findAll();

		// Page title in view page
		$data['page_title'] = 'All Blog Posts';
		// Display posts view page
		$this->view('admin/posts/categories', $data);
	}

	public function create_category(): void
	{
		$user = new User();
		$category = new Category();

		// Check if current user is looged in 
		if (!$user->logged_in())
			redirect('auth/login');

		$data['categories'] = $category->findAll();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			
			if ($category->validate($_POST)) {

				// Insert New Category into DB
				$category->insert($_POST);
				// Success Notification
				Util::setFlash('category_create_success', 'Post Category Created Successfully!!');

				redirect('admin/categories');
			}
		}


		// Page title in "view" page
		$data['page_title'] = 'Create New Category';

		// Return category view
		$this->view('admin/posts/create-category', $data);
	}

	public function comments(): void
	{
		$comment = new Comment();
		$data['comments'] = $comment->commentsWithPosts();

		// Page title in view page
		$data['page_title'] = 'Comments';
		// Display posts view page
		$this->view('admin/posts/comments', $data);
	}
}
