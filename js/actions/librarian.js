$(document).ready(function () {

    var table = $('#librariansTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "routes/librarian.php",
            type: "GET"
        },
        columns: [
            { 
                data: null, 
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: "username" },
            { 
                data: null,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info edit-btn"  data-bs-toggle="modal" data-bs-target="#editTaskModal" data-id="${row.id}" data-username="${row.username}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">Delete</button>
                    `;
                }
            }
        ],
        pageLength: 5,
        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]]
    });

    $('#addLibrarianModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    });

    $(document).on('submit', '#librarian-form', function (e) {

        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'add');

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
                        $('#librarianModal').modal('hide');
                    });
                }                
            }
        });
    });
});