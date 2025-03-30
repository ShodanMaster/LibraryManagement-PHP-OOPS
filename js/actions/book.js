$(document).ready(function () {

    var table = $('#booksTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "routes/book.php",
            type: "GET"
        },
        columns: [
            { 
                data: null, 
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: "serial_no" },
            { data: "category" },
            { data: "title" },
            { data: "author" },
            { 
                data: null,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info edit-btn" data-bs-toggle="modal" data-bs-target="#editBookModal" data-id="${row.id}" data-author="${row.author}" data-category="${row.category}" data-title="${row.title}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" data-name="${row.title}">Delete</button>
                    `;
                }
            }
        ],
        pageLength: 5,
        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]]
    });

    $.ajax({
        url: "routes/author.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            let authorSelect = $("#author, #edit-author");
            authorSelect.empty();
    
            authorSelect.append('<option value="" disabled selected>--Select Author--</option>');
    
            $.each(response.data, function (key, author) {
                authorSelect.append(
                    `<option value="${author.name}">${author.name}</option>`
                );
            });
        },
        error: function () {
            alert("Failed to load authors.");
        }
    });    

    $.ajax({
        url: "routes/category.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            let categorySelect = $("#category, #edit-category");
            categorySelect.empty();
    
            categorySelect.append('<option value="" disabled selected>--Select Category--</option>');
    
            $.each(response.data, function (key, category) {
                categorySelect.append(
                    `<option value="${category.name}">${category.name}</option>`
                );
            });
        },
        error: function () {
            alert("Failed to load categories.");
        }
    });    

    $('#addBookModal').on('shown.bs.modal', function () {
        $(this).find('form')[0].reset();
    });

    $(document).on('submit', '#book-form', function (e) {

        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'add');

        $.ajax({
            type: "POST",
            url: "routes/book.php",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
                if(response.status === 200){
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK",
                    }).then(() => {
                        table.ajax.reload();
                        $('#addBookModal').modal('hide');
                    });
                }else if(response.status === 403){
                    Swal.fire({
                        title: "Warning!",
                        text: response.message,
                        icon: "warning",
                        confirmButtonText: "OK",
                    })
                }else{
                    Swal.fire({
                        title: "Error!",
                        text: response.message,
                        icon: "error",
                        confirmButtonText: "OK",
                    })
                }             
            }
        });
    });  

    $('#booksTable').on('click', '.edit-btn', function () {
        
        var bookId = $(this).data('id');
        var category = $(this).data('category');
        var author = $(this).data('author');
        var title = $(this).data('title');
    
        $('#edit-id').val(bookId);
        $('#edit-title').val(title);
        $('#edit-category').val(category);
        $('#edit-author').val(author);
    });
    

    $(document).on('submit', '#bookEdit-form', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'edit');

        $.ajax({
            type: "POST",
            url: "routes/book.php",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
                if(response.status === 200){
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK",
                    }).then(() => {
                        table.ajax.reload();
                        $('#editBookModal').modal('hide');
                    });
                }else if(response.status === 403){
                    Swal.fire({
                        title: "Warning!",
                        text: response.message,
                        icon: "warning",
                        confirmButtonText: "OK",
                    })
                }else{
                    Swal.fire({
                        title: "Error!",
                        text: response.message,
                        icon: "error",
                        confirmButtonText: "OK",
                    })
                }                  
            }
        });
        
    });

    $('#booksTable').on('click', '.delete-btn', function(){
        var bookId = $(this).data('id');
        var name = $(this).data('name');

        Swal.fire({
            title: "Are you sure delete "+ name +"?",
            text: "You won't be able to revert this! Also Books Corresponding to this Book will be Deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "routes/book.php",
                    data: {
                        action: 'delete',
                        bookId: bookId
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            Swal.fire(
                                "Deleted!",
                                name + " has been deleted.",
                                "success"
                            );
                            table.draw();
                        } else {
                            Swal.fire(
                                "Error!",
                                response.message,
                                "error"
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            "Error!",
                            "Failed to delete "+name,
                            "error"
                        );
                    }
                });
            }
        });
    });
});