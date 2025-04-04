<div class="card shadow-lg mb-3">
    <div class="card-header bg-success text-white text-center fs-4">
        Fine Book
    </div>
    <form id="getBooksForm">
        <div class="card-body">
            <div class="form-group">
                <label for="memberSerialNo" class="form-label">Member Serial No</label>
                <input type="text" class="form-control" name="memberSerialNo" id="memberSerialNo" required>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-success btn-sm" id="add-transaction-grid">Scan</button>
        </div>
    </form>
</div>

<div id="fineBooksCard" class="card shadow-lg mb-3" style="display: none;">
    <div class="card-header bg-success text-white text-center fs-4">
       Scan Books
    </div>
    <form id="fineScanForm">
        <div class="card-body">
            <div class="form-group">
                <label for="bookSerialNo" class="form-label">Book Serial No</label>
                <input type="text" class="form-control" name="bookSerialNo" id="bookSerialNo" required>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-success btn-sm" id="add-transaction-grid">Scan</button>
        </div>
    </form>
</div>

<table class="table" id="bookTable" style="display: none;">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<script src="js/actions/transaction/fineBook.js"></script>