  <?php
/** @var array $clients */
$this->view('inc/header', $data); ?>
  <div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
      <?php $this->view('inc/welcome', $data); ?>
  </div>

  <main class="container-fluid px-4">
      <div class="row my-4">
          <div class="col">
              <div class="d-flex justify-content-between align-items-center mb-3">
                  <h3 class="fs-4 page-title">Clients List</h3>
                  <a href="<?= ROOT . '/admin/clients/pdf' ?>" class="btn btn-danger"><i class="bi bi-download me-2"></i>Download PDF</a>
                  <a href="<?= ROOT . '/admin/client/create/null' ?>" class="btn btn-warning text-dark"><i class="bi bi-plus-circle me-2"></i>Add New Client</a>
              </div>
              <?= Util::displayFlash('client_register_success', 'success') ?>
              <?= Util::displayFlash('client_update_success', 'success') ?>
              <?= Util::displayFlash('client_delete_success', 'success') ?>
              <div class="table-responsive bg-body-tertiary p-3 rounded shadow-sm animated-card" style="--animation-order: 1;">
                  <div class="table-responsive">
                      <table class="table ntoshitable table-hover table-striped align-middle mb-0 ">
                          <thead>
                              <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">Name</th>
                                  <th scope="col">Phone</th>
                                  <th scope="col">City</th>
                                  <th scope="col">Province</th>
                                  <th scope="col">Status</th>
                                  <th scope="col" class="text-end">Actions</th>
                              </tr>
                          </thead>
                          <tbody id="customer-table-body"> 
                              <?php $rowId = 1;
                              
                                if (!empty($clients)): foreach ($clients as $client): ?>
                                      <tr>
                                          <th scope="row"><?= $rowId++ ?></th>
                                          <td><img src="<?= get_image($client->image, 'user') ?>" alt="Jane Doe" class="rounded-circle me-2" style="width:30px; height:30px; object-fit:cover;"><?= $client->firstname . ' ' . $client->surname ?></td>
                                          <td><?= $client->phone ?></td>
                                          <td><?= $client->city ?></td>
                                          <td><?= $client->province ?></td>
                                          <td><span class="badge bg-success"><?= $client->status ?></span></td> 
                                          <td class="text-end">
                                              <a href="<?= ROOT . "/admin/client/profile-view/$client->user_id" ?>" class="btn btn-sm btn-outline-info me-1" title="View Client"><i class="bi bi-eye-fill"></i></a>
                                              <a href="<?= ROOT . "/admin/client/edit/$client->user_id" ?>" class="btn btn-sm btn-outline-warning me-1" title="Edit Client"><i class="bi bi-pencil-square"></i></a>
                                              <a href="<?= ROOT . "/admin/client/delete/$client->user_id" ?>" class="btn btn-sm btn-outline-warning me-1" title="Delete Client"><i class="bi bi-trash-fill"></i></a>

                                          </td>
                                      </tr>
                              <?php endforeach;
                                endif ?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
  </main>

  <!-- ======= Footer ======= -->
  <?php $this->view('inc/footer') ?>