<?php
    /**
     * @var array $rooms
     * @var array $data
     */
    $this->view('inc/header', $data); ?>
<div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
    <?php $this->view('inc/welcome', $data); ?>
</div>

<main class="container-fluid px-4">
    <div class="row my-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fs-4 page-title">Chat Rooms</h3>
                <?php
                switch (user('user_role')) {
                    case 'Admin':
                    case 'Sys Admin': ?>
                        <button class="btn btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#createRoomModal">
                            <i class="bi bi-plus-circle me-2"></i>Create Room
                        </button>
                <?php
                        break;

                    default:
                        # code...
                        break;
                }
                ?>
            </div>

            <div class="row">
                <?php if ($rooms): foreach ($rooms as $room): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card chat-room-card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= get_image($room->image, 'user') ?>" alt="<?= $room->firstname ?>"
                                            class="rounded-circle me-3" width="50" height="50">
                                        <div>
                                            <h5 class="card-title mb-0"><?= esc($room->room_name) ?></h5>
                                            <p class="text-muted mb-0">Created by <?= esc($room->firstname) ?></p>
                                        </div>
                                    </div>
                                    <a href="<?= ROOT ?>/admin/chat/room/<?= $room->id ?>" class="btn btn-outline-<?= THEME_COLOR ?> w-100">
                                        Join Chat
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach;
                else:
                    switch (user('user_role')) {
                        case 'Admin':
                        case 'Sys Admin': ?>
                            <div class="alert alert-danger text-center">
                                Oops! No Chatroom found, Make sure to create one!
                            </div>
                        <?php
                            break;

                        default: ?>
                            <div class="alert alert-danger text-center">
                                Oops! No Chatroom found, You'll have to wait for ADMINS to create one!
                            </div>
                    <?php
                            break;
                    }
                    ?>

                <?php endif ?>
            </div>
        </div>
    </div>
</main>


<!-- Create Room Modal -->
<div class="modal fade" id="createRoomModal" tabindex="-1" aria-labelledby="createRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= ROOT ?>/admin/chat/create-room">
                <div class="modal-header">
                    <h5 class="modal-title" id="createRoomModalLabel">Create New Chat Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="roomName" class="form-label">Room Name</label>
                        <input type="text" class="form-control" id="roomName" name="room_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-<?= THEME_COLOR ?>">Create Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->view('inc/footer') ?>