$(document).ready(function () {

    var table = $('#membersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "routes/Member.php",
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
            { data: "name" },
            { data: "phone" },
            { 
                data: null,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info edit-btn"  data-bs-toggle="modal" data-bs-target="#editMemberModal" data-id="${row.id}" data-name="${row.name}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" data-name="${row.name}">Delete</button>
                    `;
                }
            }
        ],
        pageLength: 5,
        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]]
    });

    $('#addMemberModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    });

  

    $(document).on('submit', '#member-form', function (e) {

        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'add');

        $.ajax({
            type: "POST",
            url: "routes/member.php",
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
                        $('#addMemberModal').modal('hide');
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

        
    document.getElementById('change-password').addEventListener('change', function () {
        
        let passwordFields = document.querySelectorAll('#edit-password, #edit-confirm-password, #edit-show-password');
        passwordFields.forEach(field => {
            field.disabled = !this.checked;
        });
    });
        
    document.getElementById('edit-show-password').addEventListener('change', function () {
        let passwordFields = document.querySelectorAll('#edit-password, #edit-confirm-password');
        passwordFields.forEach(field => {
            field.type = this.checked ? 'text' : 'password';
        });
    });

    

    $('#librariansTable').on('click', '.edit-btn', function () {
        
        var userId = $(this).data('id');
        var username = $(this).data('username');
    
        console.log(userId, username);
    
        $('#edit-id').val(userId);
        $('#edit-username').val(username);
        $('#change-password').prop('checked', false);
        $('#edit-show-password').prop('checked', false);
        $('#edit-password').val('').prop('disabled', true);
        $('#edit-confirm-password').val('').prop('disabled', true);
    
        $('#change-password').on('change', function () {
            $('#edit-password, #edit-confirm-password', '#edit-show-password').prop('disabled', !this.checked);
        });
    });
    

    $(document).on('submit', '#librarianEdit-form', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'edit');

        $.ajax({
            type: "POST",
            url: "routes/librarian.php",
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
                        $('#editLibrarianModal').modal('hide');
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

    $('#librariansTable').on('click', '.delete-btn', function(){
        var userId = $(this).data('id');
        var username = $(this).data('username');

        Swal.fire({
            title: "Are you sure delete "+ username +"?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "routes/librarian.php",
                    data: {
                        action: 'delete',
                        userId: userId
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            Swal.fire(
                                "Deleted!",
                                username + " has been deleted.",
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
                            "Failed to delete "+username,
                            "error"
                        );
                    }
                });
            }
        });
    });
});