@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h3 class="mb-1">
            User Management
        </h3>

        <p class="text-muted mb-0">
            Manage all registered users
        </p>
    </div>

    <button class="btn btn-primary" onclick="add_data();">
        <i class="fa fa-plus"></i>
        Add User
    </button>

</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped" id="table_data" style="width: 100%;">
            <thead>
                <tr>
                    <th style="width: 5%; min-width: 40px;">ID</th>
                    <th style="width: 35%; min-width: 150px;">Name</th>
                    <th style="width: 35%; min-width: 150px;">Email</th>
                    <th style="width: 15%; min-width: 90px;">Role</th>
                    <th style="width: 15%; min-width: 120px;">Created At</th>
                    <th style="width: 5%; min-width: 80px;">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="dlg" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="dlg_form" autocomplete="off">
				<div class="modal-header text-white" id="dlg_header">
					<div class="modal-title" id="dlg_title"></div>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id">
					<div class="mb-3">
						<label class="form-label">Email:</label>
						<input type="email" class="form-control" name="email" autocomplete="new-password" maxlength="255" required="required">
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="mb-3">
								<label class="form-label">Password:</label>
								<input type="password" class="form-control required-add" name="password" autocomplete="new-password" required="required">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="mb-3">
								<label class="form-label">Confirm Password:</label>
								<input type="password" class="form-control required-add" name="confirm_password" autocomplete="new-password" required="required">
							</div>
						</div>
					</div>
					<div class="mb-3">
						<label class="form-label">Name:</label>
						<input type="text" class="form-control" name="name" maxlength="255" required="required">
					</div>
					<div class="mb-3">
						<label class="form-label">Role:</label>
						<select class="form-select" name="role" required="required">
							<option value="" hidden>-- Select Role --</option>
							<option value="admin">Admin</option>
							<option value="user">User</option>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-save"><i class="fa fa-save"></i> Save</button>
					<button type="button" class="btn btn-secondary btn-close-modal" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				</div>
				<div class="modal-loading" style="display: none;">
					<div class="modal-loading-body">
						<div class="modal-loading-icon">
							<i class="fas fa-spinner fa-spin fa-4x"></i>
						</div>
					</div>
				</div>
            </form>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
var tableData = null;
var editUrl = null;

$(document).ready(function() {
	tableData = $('#table_data').DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: '{{ route('users.data') }}',
			type: 'POST'
		},
		search: {
			return: true
		},
		order: [[0, 'desc']],
		columns: [
            { data: 'id', name: 'id', searchable: false, className: 'text-center' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'role' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
		'dom': '<"row"<"col-sm-6"l><"col-sm-6"f>><"row"<"col-sm-12"<"table-responsive"t>r>><"row"<"col-sm-5"i><"col-sm-7"p>>'
	});

    $('#table_data').on('click', '.btn-edit', function () {
        editUrl = $(this).data('url');
        let tr = $(this).closest('tr');
        let data = tableData.row(tr).data();
        edit_data(data);
    });

    $('#table_data').on('click', '.btn-delete', function () {
        let url = $(this).data('url');
        let tr = $(this).closest('tr');
        let data = tableData.row(tr).data();
        delete_data(url, data);
    });

    $('#dlg_form').on('submit', function(e) {
        e.preventDefault();

        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }

        $('.modal-loading').show();

        let id = $('#dlg_form input[name="id"]').val();
        let isEdit = id !== '';
        let url = isEdit ? editUrl : '{{ route('users.store') }}';
        let method = isEdit ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $('.modal-loading').hide();
                if (response?.success) {
                    $('#dlg').modal('hide');
                    Swal.fire(
                        'Success!',
                        response.message,
                        'success'
                    );
                    tableData.draw(false);
                } else {
                    Swal.fire(
                        'Failed!',
                        response.message ?? 'Failed save data.',
                        'error'
                    );
                }
            },
            error: function(xhr) {
                $('.modal-loading').hide();
                let message = 'Failed save data.';
                if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire(
                    'Failed!',
                    message,
                    'error'
                );
            }
        });
    });

});

function add_data()
{
	$('#dlg_form')[0].reset();
	$('#dlg_form input[name="id"]').val('');

	$('#dlg_form input[name="password"]').attr('placeholder', '');
	$('#dlg_form input[name="confirm_password"]').attr('placeholder', '');

	$('#dlg_form .required-add').prop('required', true);

	$('.btn-save').removeClass('btn-success').addClass('btn-primary');
	$('#dlg_title').html('<b><i class="fa fa-plus"></i> Add User</b>');
	$('#dlg_header').removeClass('bg-success').addClass('bg-primary');
	$('.modal-loading').hide();
	$('#dlg').modal('show');
}

function edit_data(data)
{
	$('#dlg_form')[0].reset();

	$.each(data, function(k, v) {
		$('#dlg_form [name="'+k+'"]').val(v);
	});

	$('#dlg_form input[name="password"]').attr('placeholder', 'Keep empty if unchanged');
	$('#dlg_form input[name="confirm_password"]').attr('placeholder', 'Keep empty if unchanged');

	$('#dlg_form .required-add').prop('required', false);

	$('.btn-save').removeClass('btn-primary').addClass('btn-success');
	$('#dlg_title').html('<b><i class="fa fa-edit"></i> Edit User</b>');
	$('#dlg_header').removeClass('bg-primary').addClass('bg-success');
	$('.modal-loading').hide();
	$('#dlg').modal('show');
}

function delete_data(url, data)
{
    Swal.fire({
        title: 'Delete User',
        html: 'Are you sure want to delete user <b>"'+data.name+'"</b>?',
        icon: 'question',
        showCancelButton: true,
        customClass: {
            confirmButton: 'btn btn-lg btn-danger mx-1',
            cancelButton: 'btn btn-lg btn-secondary mx-1'
        },
        buttonsStyling: false,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if (result.isConfirmed)
        {
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    if(response?.success)
                    {
                        Swal.fire(
                            'Success!',
                            'Data deleted successfully.',
                            'success'
                        );
                        tableData.draw(false);
                    }
                    else
                    {
                        Swal.fire(
                            'Failed!',
                            response.message,
                            'error'
                        );
                    }
                },
                error: function() {

                    Swal.fire(
                        'Failed!',
                        'Failed delete data.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>
@endpush
