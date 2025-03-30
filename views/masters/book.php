<!-- Add Member Modal -->
<div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h1 class="modal-title fs-5" id="addBookModalLabel">Add Book</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="book-form">
        <div class="modal-body">
            <div class="form-group mb-2">
                <select class="form-control" name="author" id="author">
                    <option value="" disabled selected>--Select Author--</option>
                </select>
            </div>
            <div class="form-group mb-2">
                <input type="text" class="form-control" name="title" id="title" placeholder="Book title" required>
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

<!-- Edit Book Modal -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h1 class="modal-title fs-5" id="editBookModalLabel">Edit Book</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <form id="bookEdit-form">
          <input type="hidden" name="id" id="edit-id">
          <div class="modal-body">
          <div class="form-group mb-2">
                <select class="form-control" name="author" id="edit-author">
                    <option value="" disabled selected>--Select Author--</option>
                </select>
            </div>
            <div class="form-group mb-2">
                <input type="text" class="form-control" name="title" id="edit-title" placeholder="Book Title" required>
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
    <h1>Book Master</h1>

    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBookModal">
        Add Book
    </button>
</div>

<div class="card shadow-lg">
  <div class="card-header bg-success text-white fs-4">
    Books Table
  </div>
  <div class="card-body">
    <table id="booksTable" class="display">
          <thead>
              <tr>
                  <th>#</th>
                  <th>Serial No</th>
                  <th>title</th>
                  <th>author</th>
                  <th>Action</th>
              </tr>
          </thead>
          <tbody></tbody>
      </table>
  </div>
</div>

<script src="js/actions/book.js"></script>