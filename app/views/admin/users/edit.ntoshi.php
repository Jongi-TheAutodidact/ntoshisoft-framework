<?php

/**
 * @var string $page_title
 * @var array $data
 * @var object $row
 * @var array $errors
 */
$this->view('inc/header', $data)
?>

<main id="main" class="main">

    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="" id="user-update" enctype="multipart/form-data">
                            <!--CSRF TOKEN-->
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
                            <!--USER EDITING RECORD-->
                            <input type="hidden" name="<?= esc('updated_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            <!--DATE RECORD UPDATED-->
                            <input type="hidden" name="<?= esc('date_updated') ?>" value="<?= date('Y-m-d H:i:s') ?>">
                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors);  ?>
                                </div>
                            <?php endif; ?>
                            <!--ROW 1-->

                            <div class="row form-row">
                                <div class="col-lg-12 text-center">
                                    <label> Profile Image <br>
                                        <img src="<?= old_value(get_image($row->image), get_image($row->image, 'user'))  ?>" class="rounded-circle" width="80px" height="80px" style=" object-fit:cover;cursor:pointer">
                                        <input onchange="display_image(this.files[0], event)" type="file" value="<?= old_value('image', $row->image) ?>" name="<?= esc('image') ?>" class="d-none">
                                    </label>
                                    <br> <span style="font-size: 0.6rem;" class="text-danger fw-bold">***You will still need to upload an image even if its the same***</span>
                                </div>
                            </div>
                            <hr>
                            <!--ROW 2-->
                            <div class="row form-row">
                                <div class="col-lg-4">
                                    <label for="firstname">First Name</label>
                                    <input type="text" name="<?= esc('firstname') ?>" value="<?= old_value('firstname', $row->firstname) ?>" class="form-control mb-1" id="firstname">
                                </div>
                                <div class="col-lg-4">
                                    <label for="surname">Surname</label>
                                    <input type="text" name="<?= esc('surname') ?>" value="<?= old_value('surname', $row->surname) ?>" class="form-control mb-1" id="surname">
                                </div>
                                <div class="col-lg-4">
                                    <label for="username">Username</label>
                                    <input type="text" name="<?= esc('username') ?>" value="<?= old_value('username', $row->username) ?>" class="form-control mb-1" id="username">
                                </div>
                            </div>


                            <!--ROW 4-->
                            <div class=" row form-row">
                                <div class="col-lg-4">
                                    <label for="email">email</label>
                                    <input type="email" name="<?= esc('email') ?>" value="<?= old_value('email', $row->email) ?>" class="form-control mb-1" id="email">
                                </div>
                                <div class="col-lg-4">
                                    <label for="password">Password</label>
                                    <input type="password" name="<?= esc('password') ?>" value="" class="form-control mb-1" id="password" placeholder="Leave Black to keep old password">
                                </div>
                                <div class="col-lg-4">
                                    <label for="user_role">User Role</label>
                                    <?php $selUserRole = $row->user_role ?>
                                    <select name="<?= esc('user_role', $row->user_role) ?>" id="user_role" class="form-control mb-1">
                                        <option value="Select Role">--Select User Role--</option>
                                        <?php if (!empty(USER_ROLES)): foreach (USER_ROLES as $role): ?>
                                                <option value="<?= $role ?>" <?= $selUserRole == $role ? 'selected' : '' ?>><?= $role ?></option>
                                            <?php endforeach;
                                        else: ?>
                                            <option value="Admin" <?= $selUserRole == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="User" <?= $selUserRole == 'User' ? 'selected' : '' ?>>User</option>
                                            <option value="Customer" <?= $selUserRole == 'Customer' ? 'selected' : '' ?>>Customer</option>
                                        <?php endif ?>
                                    </select>
                                </div>
                            </div>
                            <div class=" row form-row">
                                <div class="col-lg-4">
                                    <label for="user_id">User ID</label>
                                    <input type="text" name="<?= esc('user_id') ?>" value="<?= $row->user_id ?>" class="form-control mb-1" id="user_id" readonly>
                                </div>
                                <div class="col-lg-4">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="<?= esc('phone') ?>" value="<?= old_value('phone', $row->phone) ?>" class="form-control mb-1" id="phone">
                                </div>

                                <div class="col-lg-4">
                                    <label for="gender">Gender</label>
                                    <?php $genSelect = old_value('gender', $row->gender) ?>
                                    <select name="<?= esc('gender') ?>" class="form-control mb-1" id="gender">
                                        <option value="Male" <?= $genSelect == 'Male' ? 'selected' : ''  ?>>Male</option>
                                        <option value="Female" <?= $genSelect == 'Female' ? 'selected' : ''  ?>>Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">EDIT USER</button>
                                    <?php
                                    switch (user('user_role')) {
                                        case 'Admin': ?>
                                            <a href="<?= ROOT ?>/admin/users" class="btn btn-danger">CANCEL</a>
                                        <?php

                                            break;

                                        default: ?>
                                            <a href="<?= ROOT ?>/admin" class="btn btn-danger">CANCEL</a>
                                    <?php

                                            break;
                                    }
                                    ?>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->
<script>
    /**
     * Change Image
     * @param {*} file  
     * */
    function change_image(file) {
        var obj = {};
        obj.image = file;
        obj.data_type = "profile-image";
        obj.id = "<?= user('id') ?>";

        send_data(obj);
    }

    /**
     * Send Data
     * @param {*} obj  
     * */
    function send_data(obj) {
        var kfForm = new FormData();
        for (key in obj) {
            kfForm.append(key, obj[key]);
        }
        var ajax = new XMLHttpRequest();
        // Add event listener
        ajax.addEventListener('readystatechange', (e) => {
            if (ajax.readyState == 4 && ajax.status == 200) {
                handle_result(ajax.responseText);
            }
        });

        ajax.open('post', '<?= ROOT ?>/ajax', true);
        ajax.send(kfForm);

        /**
         * Handle result
         * @param {*} result  
         * */
        function handle_result(result) {
            alert(result);
            console.log(result);
        }
    }
</script>

<!-- ======= Footer ======= -->
<?php $this->view('inc/footer') ?>