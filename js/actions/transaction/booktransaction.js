$(document).ready(function () {
    
    let bookIdArray = [];

    $(document).on('submit', '#add-form', function (e) {
        e.preventDefault();

        let member = document.getElementById('memberSerailNo').value;
        let book = document.getElementById('bookSerailNo').value;

        let formData = new FormData();
        formData.append('member', member);
        formData.append('book', book);
        formData.append('action', 'fetchData');

        $.ajax({
            type: "POST",
            url: "routes/transactions/booktransaction.php",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log('Raw Response:', response); // Log raw response to debug
        
                try {
                    let data = JSON.parse(response);
                    console.log('Parsed Response:', data);
        
                    if (data.status === 200) {
                        // Set member details
                        document.getElementById('memberName').value = data.member.name;
                        document.getElementById('memberId').value = data.member.id;
                        document.getElementById('memberSerialNo').value = member;
        
                        // Add book ID to the array if not already added
                        if (!bookIdArray.includes(data.book.id)) {
                            bookIdArray.push(data.book.id);
                            document.getElementById('bookIds').value = JSON.stringify(bookIdArray);
        
                            // Append book to the table
                            let tableBody = document.querySelector('#bookTable tbody');
                            let newRow = `<tr id="row-${data.book.id}">
                                            <td>${bookIdArray.length}</td>
                                            <td>${data.book.title}</td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-book" data-id="${data.book.id}">Remove</button></td>
                                          </tr>`;
                            tableBody.insertAdjacentHTML('beforeend', newRow);
                        } else {
                            Swal.fire({
                                icon: "warning",
                                title: "Duplicate Book",
                                text: "This book has already been added."
                            });
                        }
                    } else if (data.status === 400) {
                        // Handle the case when the book is already issued
                        Swal.fire({
                            icon: "warning",
                            title: "Book Already Issued",
                            text: data.message // The message will be something like "The Vishnu book is already issued."
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Invalid response from the server."
                        });
                    }
                } catch (error) {
                    console.log('Response: '+response);
                     
                    if(response.status===400){
                        Swal.fire({
                            icon: "warning",
                            title: "Book Already Issued",
                            text: response.message // Log raw response here for debugging
                        });
                    }

                    Swal.fire({
                        icon: "error",
                        title: "JSON Parse Error",
                        text: "Failed to parse server response. Raw response: " + response // Log raw response here for debugging
                    });
                    console.log('Error:', error); // Log error for debugging
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

    // Handle Book Removal
    $(document).on('click', '.remove-book', function () {
        let bookId = $(this).data('id');
        bookIdArray = bookIdArray.filter(id => id !== bookId);
        document.getElementById('bookIds').value = JSON.stringify(bookIdArray);

        // Remove row from table
        $(`#row-${bookId}`).remove();
    });

    $(document).on('submit', '#bookTransaction', function(e) {
        e.preventDefault();
    
        var formData = new FormData(this);
        formData.append('action', 'bookTransaction');
    
        $.ajax({
            type: "POST",
            url: "routes/transactions/booktransaction.php",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                try {
                    let data = JSON.parse(response); 
    
                    if (data.status == 200) {
                        Swal.fire({
                            icon: "success",
                            title: "Transaction Successful",
                            text: "The book transaction has been recorded successfully.",
                            timer: 2000,
                            showConfirmButton: false
                        });
    
                        // Reset Form Fields
                        $('#bookTransaction')[0].reset();
    
                        // Clear Book Table and ID Storage
                        $('#bookTable tbody').empty();
                        $('#bookIds').val("[]");
                        bookIdArray = [];
    
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Transaction Failed",
                            text: data.message || "Something went wrong."
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: "error",
                        title: "Error Parsing Response",
                        text: "Invalid server response. Please try again."
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: "error",
                    title: "Request Failed",
                    text: "There was an issue connecting to the server."
                });
            }
        });
    });
    


});