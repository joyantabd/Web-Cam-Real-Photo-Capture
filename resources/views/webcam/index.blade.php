@extends('layouts.app')

@section('title','Web-Cam')

@push('css')

    <style type="text/css">

        #results {  background:#ccc; }
    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <br>
        <button type="button" name="create_record" id="create_record" data-toggle="modal"  class="btn btn-info float-right mb-2">
            <i class="fa fa-plus"></i> Add  Appointment</button>
        <br>
        <div class="container-fluid">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="webcam_table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection



@push('scripts')
    <div id="formModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">New Image Capture</h4>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <form method="post" id="sample_form" class="form-horizontal" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="control-label col-md-3" >Name<big class="text-danger">*</big></label>
                            <div class="col-md-8">
                                <input type="text"  name="name" id="name" class="form-control" placeholder="Enter Name"  required/>
                            </div>
                        </div>




                        <div class="form-group hide_in_edit">
                            <label class="control-label col-md-3">Image<big class="text-danger">*</big></label>
                            <div class="col-md-1">
                                <button type="button" value="Take Photo"  onClick="take_snapshot()"><i class="fa fa-camera"></i></button>
                            </div>
                            <div class="col-md-3">
                                <div id="my_camera"  style="margin-top: 2px"></div>
                            </div>
                            <div class="col-md-4">
                                <div id="results" class="img-thumbnail">Your Image</div>
                                <input style="display:none;" name="image" id="image" class="form-control" />
                            </div>
                        </div>


                        <div class="form-group" align="center">
                            <input type="hidden" name="action" id="action" />
                            <input type="hidden" name="hidden_id" id="hidden_id" />
                            <input type="submit" name="action_button" id="action_button" class="btn btn-success" value="Add" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div id="confirmModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-confirm">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="icon-box">
                        <i class="material-icons">&#xE5CD;</i>
                    </div>
                    <h4 class="modal-title">Are you sure?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete these records? This process cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                    <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        Webcam.set({
            width: 140,
            height: 100,
            image_format: 'jpeg',
            jpeg_quality: 90,
        });
        Webcam.attach( '#my_camera' );


        function take_snapshot() {
            // take snapshot and get image data
            Webcam.snap( function(data_uri) {
                // display results in page
                document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
                $("#image").attr('value',data_uri);

            } );
        }

        $(document).ready(function() {

            $('#webcam_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('webcam.index') }}",
                },
                columns: [
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'image',
                        name: 'image',
                        render: function (data, type, full, meta) {
                            return "<div class='zoom'><img src={{ URL::to('/') }}/images/joy/" + data + " width='35' height='35' class='img-circle' /></div>";
                        },
                        orderable: false
                    },


                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }

                ]
            });

            $('#create_record').click(function () {
                $('.modal-title').text("Add New Record");
                $('#action_button').val("Add");
                $('#action').val("Add");
                $('#formModal').modal('show');
            });

            $('#sample_form').on('submit', function (event) {
                event.preventDefault();
                if ($('#action').val() == 'Add') {
                    $.ajax({
                        url: "{{ route('webcam.store') }}",
                        method: "POST",
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        dataType: "json",
                        success: function (data) {
                            var html = '';
                            if (data.errors) {
                                html = '<div class="alert alert-danger">';
                                for (var count = 0; count < data.errors.length; count++) {
                                    html += '<p>' + data.errors[count] + '</p>';
                                }
                                html += '</div>';
                            }
                            if (data.success) {
                                html = '<div class="alert alert-success">' + data.success + '</div>';
                                $('#sample_form')[0].reset();
                                $('#webcam_table').DataTable().ajax.reload();


                            }
                            $('#form_result').html(html);
                        }
                    })
                }

                if ($('#action').val() == "Edit") {
                    $.ajax({
                        url: "{{ route('webcam.update') }}",
                        method: "POST",
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        dataType: "json",
                        success: function (data) {
                            var html = '';
                            if (data.errors) {
                                html = '<div class="alert alert-danger">';
                                for (var count = 0; count < data.errors.length; count++) {
                                    html += '<p>' + data.errors[count] + '</p>';
                                }
                                html += '</div>';
                            }
                            if (data.success) {
                                html = '<div class="alert alert-success">' + data.success + '</div>';
                                $('#sample_form')[0].reset();
                                $('#webcam_table').DataTable().ajax.reload();


                            }
                            $('#form_result').html(html);
                        }
                    });
                }
            });

            $(document).on('click', '.edit', function () {
                var id = $(this).attr('id');
                $('#form_result').html('');
                $.ajax({
                    url: "webcam/" + id + "/edit",
                    dataType: "json",
                    success: function (html) {
                        $('#name').val(html.data.name);
                        $('#hidden_id').val(html.data.id);
                        $('.modal-title').text("Edit Record");
                        $('#action_button').val("Edit");
                        $('#action').val("Edit");
                        $('.hide_in_edit').html('');
                        $('#formModal').modal('show');

                    }
                })
            })
        });


    </script>

@endpush

