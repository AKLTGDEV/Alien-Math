<?php

use App\users;
use Illuminate\Support\Facades\Input;

?>

<script>
    /*  ==========================================
    SHOW UPLOADED IMAGE
* ========================================== */
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#imageResult')
                    .attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(function() {
        $('#upload').on('change', function() {
            readURL(input);
        });
    });

    /*  ==========================================
        SHOW UPLOADED IMAGE NAME
    * ========================================== */
    var input = document.getElementById('upload');
    var infoArea = document.getElementById('upload-label');

    input.addEventListener('change', showFileName);

    function showFileName(event) {
        var input = event.srcElement;
        var fileName = input.files[0].name;
        infoArea.textContent = 'File name: ' + fileName;
    }
</script>

<style>
    #upload {
        opacity: 0;
    }

    #upload-label {
        position: absolute;
        top: 50%;
        left: 1rem;
        transform: translateY(-50%);
    }

    .image-area {
        border: 2px dashed rgba(255, 255, 255, 0.7);
        padding: 1rem;
        position: relative;
    }

    .image-area::before {
        content: 'Uploaded image result';
        color: #fff;
        font-weight: bold;
        text-transform: uppercase;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 0.8rem;
        z-index: 1;
    }

    .image-area img {
        z-index: 2;
        position: relative;
    }
</style>

<div class="modal fade" id="ProfileEditModal" tabindex="-1" role="dialog" aria-labelledby="ProfileEditModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="updateform" action="{{ route('usereditsubmit') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title" id="ProfileEditModalLabel">Edit profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row holder">

                        <div class="error-holder">
                            @if ( count( $errors ) > 0 )
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="image-area mt-4">
                                <img id="imageResult" src="#" alt="" class="img-fluid rounded-circle img-circle shadow-sm mx-auto d-block">
                            </div>
                            <div class="row">
                                <div class="input-group mb-3 px-2 py-2 rounded-pill bg-white shadow-sm">
                                    <input name="img" id="upload" type="file" onchange="readURL(this);" class="form-control">
                                    <label id="upload-label" for="upload" class="font-weight-light">Choose Image</label>
                                    <!--<label for="upload" class="btn btn-light m-0 rounded-pill px-4">
                                    <i class="fa fa-cloud-upload mr-2 text-muted"></i>
                                    <small class="text-uppercase font-weight-bold text-muted">Choose file</small>
                                </label>-->
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8 info">
                            <div class="row" style="padding:1%; margin:1%;">
                                <p>
                                    <h4>Tags</h4>
                                </p>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2 bootstrap-tagsinput">
                                    <input class="form-control" type="text" value="{{ implode(',' , json_decode(users::gettags($user->username))) }}" name="tags" data-role="tagsinput" id="tags">
                                </div>
                            </div>

                            <div class="row" style="padding:1%; margin:1%;">
                                <p>
                                    <h4>Bio</h4>
                                </p>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2 bio">
                                    <input class="form-control" type="text" value="{{ users::getbio($user->username) }}" name="bio" id="bio">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updatebtn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>