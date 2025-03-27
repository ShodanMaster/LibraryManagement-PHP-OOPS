<!-- Modal -->
<div class="modal fade" id="addLibrarianModal" tabindex="-1" aria-labelledby="addLibrarianModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h1 class="modal-title fs-5" id="addLibrarianModalLabel">Modal title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="librarian-form">
          <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <input type="text" class="form-control" name="username" id="username" placeholder="Librarian Username" required autocomplete="off">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Librarian Password" required autocomplete="new-password">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <input type="password" class="form-control" name="password_confirmation" id="confirm-password" placeholder="Confirm Password" required autocomplete="new-password">
                    </div>
                </div>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between mb-3">
    <h1>Librarian Master</h1>

    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addLibrarianModal">
        Add Librarian
    </button>
</div>

<div class="card shadow-lg">
  <div class="card-header bg-success text-white fs-4">
    Librarians Table
  </div>
  <div class="card-body">
    <table id="librariansTable" class="display">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>username</th>
                  <th>Action</th>
              </tr>
          </thead>
          <tbody></tbody>
      </table>
  </div>
</div>

<script src="js/actions/librarian.js"></script>