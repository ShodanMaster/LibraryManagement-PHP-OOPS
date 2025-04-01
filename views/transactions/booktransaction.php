<div class="card shadow-lg">
    <div class="card-header bg-success text-white text-center fs-4">
        Book Transaction
    </div>
    <form action="">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="memberSerailNo" class="form-label">Member Serial No</label>
                        <input type="text" class="form-control" name="memberSerailNo" id="memberSerailNo">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bookSerailNo" class="form-label">Book Serial No</label>
                        <input type="text" class="form-control" name="bookSerailNo" id="bookSerailNo">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="button" class="btn btn-success btn-sm" id="add-transaction-grid">Add</button>
        </div>
    </form>
</div>
<script src="js/actions/transaction/booktransaction.js"></script>