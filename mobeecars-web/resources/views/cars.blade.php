@extends('layouts.app')

@section('title', 'Car Inventory')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h3 class="mb-1">
            Car Inventory
        </h3>

        <p class="text-muted mb-0">
            Manage car inventories
        </p>
    </div>

    <button class="btn btn-primary" onclick="add_data();">
        <i class="fa fa-plus"></i>
        Add Car
    </button>

</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped" id="table_data" style="width: 100%;">
            <thead>
                <tr>
                    <th style="width: 5%; min-width: 40px;">ID</th>
                    <th style="width: 20%; min-width: 100px;">Brand</th>
                    <th style="width: 20%; min-width: 100px;">Model</th>
                    <th style="width: 20%; min-width: 100px;">Type</th>
                    <th style="width: 20%; min-width: 100px;">Image</th>
                    <th style="width: 10%; min-width: 120px;">Created At</th>
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
			<form id="dlg_form">
				<div class="modal-header text-white" id="dlg_header">
					<div class="modal-title" id="dlg_title"></div>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id">
					<div class="mb-3">
						<label class="form-label">Brand:</label>
						<select class="form-select" name="brand" required="required">
							<option value="" hidden>-- Select Brand --</option>
							<option value="Toyota">Toyota</option>
							<option value="Daihatsu">Daihatsu</option>
                            <option value="Honda">Honda</option>
                            <option value="Suzuki">Suzuki</option>
                            <option value="Mitsubishi">Mitsubishi</option>
                            <option value="Nissan">Nissan</option>
                            <option value="Hyundai">Hyundai</option>
                            <option value="Wuling">Wuling</option>
                            <option value="Kia">Kia</option>
                            <option value="Mazda">Mazda</option>
                            <option value="BMW">BMW</option>
                            <option value="Mercedes-Benz">Mercedes-Benz</option>
                            <option value="Tesla">Tesla</option>
                        </select>
					</div>
					<div class="mb-3">
						<label class="form-label">Model:</label>
						<input type="text" class="form-control" name="model" required="required">
					</div>
					<div class="mb-3">
						<label class="form-label">Type:</label>
						<select class="form-select" name="type" required="required">
							<option value="" hidden>-- Select Type --</option>
							<option value="MPV">MPV</option>
                            <option value="SUV">SUV</option>
                            <option value="Pickup">Pickup</option>
                            <option value="Hatchback">Hatchback</option>
                            <option value="Sedan">Sedan</option>
                            <option value="Electric">Electric</option>
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label">Image:</label>
						<input type="file" class="form-control" name="image" accept="image/*" id="file_input">
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

<div class="modal fade" tabindex="-1" id="dlg_viewer" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
            <div class="modal-header bg-info text-white">
                <div class="modal-title"><b><i class="fa fa-image"></i> View Car Image</b></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="" alt="Car Image" id="img_viewer" class="img-responsive w-100">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close-modal" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
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
			url: '{{ route('cars.data') }}',
			type: 'POST'
		},
		search: {
			return: true
		},
		order: [[0, 'desc']],
		columns: [
            { data: 'id', name: 'id', searchable: false, className: 'text-center' },
            { data: 'brand', name: 'brand' },
            { data: 'model', name: 'model' },
            { data: 'type', name: 'type' },
            { data: 'image', name: 'image', orderable: false, searchable: false },
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
        let url = isEdit ? editUrl : '{{ route('cars.store') }}';

        let formData = new FormData(this);

        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
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

	$('#dlg_form input[name="image"]').attr('placeholder', '').prop('required', true);

	$('.btn-save').removeClass('btn-success').addClass('btn-primary');
	$('#dlg_title').html('<b><i class="fa fa-plus"></i> Add Car</b>');
	$('#dlg_header').removeClass('bg-success').addClass('bg-primary');
	$('.modal-loading').hide();
	$('#dlg').modal('show');
}

function edit_data(data)
{
	$('#dlg_form')[0].reset();

	$.each(data, function(k, v) {
        if (k !== 'image') {
		    $('#dlg_form [name="'+k+'"]').val(v);
        }
	});

	$('#dlg_form input[name="image"]').attr('placeholder', 'Keep empty if unchanged').prop('required', false);

	$('.btn-save').removeClass('btn-primary').addClass('btn-success');
	$('#dlg_title').html('<b><i class="fa fa-edit"></i> Edit Car</b>');
	$('#dlg_header').removeClass('bg-primary').addClass('bg-success');
	$('.modal-loading').hide();
	$('#dlg').modal('show');
}

function delete_data(url, data)
{
    Swal.fire({
        title: 'Delete Car',
        html: 'Are you sure want to delete car <b>"'+data.brand+' '+data.model+'"</b>?',
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

function show_image(elm)
{
    $('#img_viewer').attr('src', $(elm).attr('src'));
    $('#dlg_viewer').modal('show');
}
</script>
@endpush
