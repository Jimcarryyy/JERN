$(document).ready(function () {
    // Add Fund
    $('#addFundForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: '../user/add_fund.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                location.reload();
            }
        });
    });

    // Edit Fund
    $('.edit-fund').click(function () {
        var id = $(this).data('id');
        $.ajax({
            url: '../user/get_fund.php',
            type: 'GET',
            data: { id: id },
            success: function (response) {
                var fund = JSON.parse(response);
                $('#editId').val(fund.id);
                $('#editFirstName').val(fund.firstName);
                $('#editLastName').val(fund.lastName);
                $('#editDateIssued').val(fund.dateIssued);
                $('#editAmount').val(fund.amount);
                $('#editFundModal').modal('show');
            }
        });
    });

    // Update Fund
    $('#editFundForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: '../user/update_fund.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                location.reload();
            }
        });
    });

    // Delete Fund
    $('.delete-fund').click(function () {
        if (confirm('Are you sure you want to delete this fund?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '../user/delete_fund.php',
                type: 'POST',
                data: { id: id },
                success: function (response) {
                    location.reload();
                }
            });
        }
    });

    
});