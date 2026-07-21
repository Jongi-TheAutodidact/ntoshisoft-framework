<?php
defined('ROOTPATH') or exit('Access Denied!');

/**
 * Bulletin Board Controller
 */
class BulletinBoardController
{
    use Controller;

    public function index(): void
    {
        $model = new BulletinBoardModel();
        $user = new User();

        if (!$user->logged_in()) {
            redirect('login');
        }

        $data['rows'] = $model->findAll() ?? [];
        $data['pinned'] = $model->getPinnedPosts('all') ?? [];
        $data['page_title'] = 'Bulletin Board Management';

        $this->view('admin/bulletin/bulletin', $data);
    }

    public function create(): void
    {
        $model = new BulletinBoardModel();
        $user = new User();

        if (!$user->logged_in()) {
            redirect('login');
        }

        $folder = 'uploads/bulletin/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($model->validate($_POST)) {
                $_POST['post_id'] = bin2hex(random_bytes(16));
                $_POST['author_id'] = user('id');
                $_POST['author_name'] = user('firstname') . ' ' . user('surname');
                $_POST['created_by'] = user('firstname') . ' ' . user('surname');
                $_POST['updated_by'] = user('firstname') . ' ' . user('surname');

                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
                    $destination = $folder . time() . '_' . $_FILES['attachment']['name'];
                    move_uploaded_file($_FILES['attachment']['tmp_name'], $destination);
                    $_POST['attachment'] = $destination;
                }

                $model->insert($_POST);

                Util::setFlash('bulletin_success', 'Post created successfully!');
                redirect('admin/bulletin');
            }
        }

        $data['errors'] = $model->errors;
        $data['page_title'] = 'Create New Post';

        $this->view('admin/bulletin/bulletin-create', $data);
    }

    public function edit(?string $id = null): void
    {
        $model = new BulletinBoardModel();
        $user = new User();

        if (!$user->logged_in()) {
            redirect('login');
        }

        $data['row'] = $model->first(['id' => $id]);

        if (!$data['row']) {
            Util::setFlash('bulletin_error', 'Post not found!');
            redirect('admin/bulletin');
        }

        $folder = 'uploads/bulletin/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($model->validate($_POST, $id)) {
                $_POST['updated_by'] = user('firstname') . ' ' . user('surname');

                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
                    $destination = $folder . time() . '_' . $_FILES['attachment']['name'];
                    move_uploaded_file($_FILES['attachment']['tmp_name'], $destination);
                    $_POST['attachment'] = $destination;
                }

                $model->update($id, $_POST);

                Util::setFlash('bulletin_success', 'Post updated successfully!');
                redirect('admin/bulletin');
            }
        }

        $data['errors'] = $model->errors;
        $data['page_title'] = 'Edit Post';

        $this->view('admin/bulletin/bulletin-edit', $data);
    }

    public function bulletinView(?string $id = null): void
    {
        $model = new BulletinBoardModel();
        $user = new User();

        if (!$user->logged_in()) {
            redirect('login');
        }

        $data['row'] = $model->first(['id' => $id]);

        if (!$data['row']) {
            Util::setFlash('bulletin_error', 'Post not found!');
            redirect('admin/bulletin');
        }

        $data['page_title'] = 'View Post';

        $this->view('admin/bulletin/bulletin-view', $data);
    }

    public function delete(?string $id = null): void
    {
        $model = new BulletinBoardModel();
        $user = new User();

        if (!$user->logged_in()) {
            redirect('login');
        }

        $model->update($id, ['status' => 'archived', 'updated_by' => user('firstname') . ' ' . user('surname')]);
        Util::setFlash('bulletin_success', 'Post archived successfully!');

        redirect('admin/bulletin');
    }

    public function public_board(): void
    {
        $model = new BulletinBoardModel();
        $user = new User();

        $audience = 'all';
        if ($user->logged_in() && $_SESSION['userRole'] == 'Parent') {
            $audience = 'parents';
        } elseif ($user->logged_in() && $_SESSION['userRole'] == 'Teacher') {
            $audience = 'teachers';
        }

        $data['posts'] = $model->getPublishedPosts($audience) ?? [];
        $data['pinned'] = $model->getPinnedPosts($audience) ?? [];
        $data['page_title'] = 'Bulletin Board';

        $this->view('front/bulletin/public-board', $data);
    }
}
