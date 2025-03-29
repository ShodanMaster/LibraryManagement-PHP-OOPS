<!-- Add Member Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h1 class="modal-title fs-5" id="addCategoryModalLabel">Add Category</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="category-form">
        <div class="modal-body">
            <div class="form-group mb-2">
                <input type="text" class="form-control" name="name" id="name" placeholder="Category Name" required>
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

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h1 class="modal-title fs-5" id="editCategoryModalLabel">Edit Category</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <form id="categoryEdit-form">
          <input type="hidden" name="id" id="edit-id">
          <div class="modal-body">
            <div class="form-group mb-2">
                <input type="text" class="form-control" name="name" id="edit-name" placeholder="Category Name" required>
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
    <h1>Category Master</h1>

    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        Add Category
    </button>
</div>

<div class="card shadow-lg">
  <div class="card-header bg-success text-white fs-4">
    Categories Table
  </div>
  <div class="card-body">
    <table id="categoriesTable" class="display">
          <thead>
              <tr>
                  <th>#</th>
                  <th>Category</th>
                  <th>Action</th>
              </tr>
          </thead>
          <tbody></tbody>
      </table>
  </div>
</div>

<script src="js/actions/category.js"></script>