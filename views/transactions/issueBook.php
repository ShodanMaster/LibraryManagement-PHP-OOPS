<div class="card shadow-lg">
    <div class="card-header bg-success text-white text-center fs-4">
        Issue Book
    </div>
    <form id="add-form">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="memberSerailNo" class="form-label">Member Serial No</label>
                        <input type="text" class="form-control" name="memberSerailNo" id="memberSerailNo" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bookSerailNo" class="form-label">Book Serial No</label>
                        <input type="text" class="form-control" name="bookSerailNo" id="bookSerailNo" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-success btn-sm" id="add-transaction-grid">Add</button>
        </div>
    </form>
</div>


<form id="bookTransaction">
    <input type="hidden" name="memberId" id="memberId">
    <input type="hidden" name="bookIds" id="bookIds">

    <div class="d-flex justify-content-between">
        <div class="form-group">
            <label for="memberName" class="form-label fs-4">Member</label>
            <input type="text" class="form-control" name="memberName" id="memberName" readonly>
        </div>
        <div class="form-group">
            <label for="memberSerialNo" class="form-label fs-4">Serial No:</label>
            <input type="text" class="form-control" name="memberSerialNo" id="memberSerialNo" readonly>
        </div>
    </div>
    <table class="table mt-3" id="bookTable">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Book Title</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-success">Submit</button>
</div>
</form>

<script src="js/actions/transaction/booktransaction.js"></script>