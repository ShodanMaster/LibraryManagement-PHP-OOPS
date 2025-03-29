<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h1 class="modal-title fs-5" id="addMemberModalLabel">Add Member</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="member-form">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-2">
                <input type="text" class="form-control" name="name" id="name" placeholder="Member Name" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-2">
                <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" required>
              </div>
            </div>
            <div class="col-md-12">
              <select class="form-control" name="type" id="type" required>
                <option value="" disabled selected>-- Select Membership Type --</option>
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
              </select>
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

<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h1 class="modal-title fs-5" id="editMemberModalLabel">Edit Member</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <form id="memberEdit-form">
          <input type="hidden" name="id" id="edit-id">
          <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-2">
                <input type="text" class="form-control" name="name" id="edit-name" placeholder="Member Name" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control" name="phone" id="edit-phone" placeholder="Phone" required>
              </div>
            </div>
            <div class="col-md-12">
              <select class="form-control" name="type" id="edit-type" required>
                <option value="" disabled selected>-- Select Membership Type --</option>
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
              </select>
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
    <h1>Member Master</h1>

    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMemberModal">
        Add Member
    </button>
</div>

<div class="card shadow-lg">
  <div class="card-header bg-success text-white fs-4">
    Members Table
  </div>
  <div class="card-body">
    <table id="membersTable" class="display">
          <thead>
              <tr>
                  <th>#</th>
                  <th>Serial No</th>
                  <th>Name</th>
                  <th>Phone</th>
                  <th>Membertship Type</th>
                  <th>Membership Updated</th>
                  <th>Status</th>
                  <th>Action</th>
              </tr>
          </thead>
          <tbody></tbody>
      </table>
  </div>
</div>

<script src="js/actions/member.js"></script>