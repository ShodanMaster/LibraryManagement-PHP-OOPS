$(document).ready(function () {
    $(document).on('submit', '#getBooksForm', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'fineBooks');

        $.ajax({
            type: "POST",
            url: "routes/transactions/booktransaction.php",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {                
                $('#fineBooksCard').show();
                $('#bookTable').show();
                try {
                    let data = JSON.parse(response);
                    if (data.status === 200) {
                        let books = data.data;
                        let tableBody = document.querySelector('#bookTable tbody');
                        tableBody.innerHTML = "";
            
                        books.forEach((book, index) => {
                            let dueClass = book.isDue === "Yes" ? "table-danger" : "";
                            let dueMessage = book.isDue === "Yes" ? `<span class="text-danger">This book is overdue!</span>` : "";
                            
                            let newRow = `<tr id="row-${book.bookSNO}" class="${dueClass}">
                                            <td>${index + 1}</td>
                                            <td>${book.bookTitle} ${dueMessage}</td>
                                          </tr>`;
                            tableBody.insertAdjacentHTML('beforeend', newRow);
                        });
                    } else {
                        $('#fineBooksCard').hide();
                        $('#bookTable').hide();
                        Swal.fire({
                            icon: "error",
                            title: "No Books Found",
                            text: data.message
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: "error",
                        title: "JSON Parse Error",
                        text: "Failed to parse server response."
                    });
                }
            },            
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "AJAX Request Failed",
                    text: "There was an issue connecting to the server."
                });
            }
        });
    });

    $(document).on('submit', '#fineScanForm', function (e) {
        e.preventDefault();

        var memberSerialNo = document.getElementById('memberSerialNo').value;
        var bookSerialNo = document.getElementById('bookSerialNo').value;
        var formData = new FormData();
        formData.append('memberSerialNo', memberSerialNo);
        formData.append('bookSerialNo', bookSerialNo);
        formData.append('action', 'fineScan');

        $.ajax({
            type: "POST",
            url: "routes/transactions/booktransaction.php",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                try {
                    let data = JSON.parse(response);

                    if (data.status === 200) {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: data.message
                        });
                        $("#row-" + bookSerialNo).remove();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: data.message
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: "error",
                        title: "JSON Parse Error",
                        text: "Failed to parse server response."
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "AJAX Request Failed",
                    text: "There was an issue connecting to the server."
                });
            }
        });
    });


});