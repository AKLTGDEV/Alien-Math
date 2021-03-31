@extends('layouts.app')

@section('content')

<script src="{{ asset('thirdparty/croppie/croppie.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('thirdparty/croppie/croppie.min.css') }}">

<?php

use App\users;
use Illuminate\Support\Facades\Auth;

?>

<script>
    jQuery(document).ready(function() {
        // Start upload preview image
        $(".gambar").attr("src", "{{ config('app.url') }}/user/{{ Auth::user()->username }}/profilepic");
        var $uploadCrop,
            tempFilename,
            rawImg,
            imageId;

        function readFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.upload-demo').addClass('ready');
                    $('#cropImagePop').modal('show');
                    rawImg = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                swal("Sorry - you're browser doesn't support the FileReader API");
            }
        }

        $uploadCrop = $('#upload-demo').croppie({
            viewport: {
                width: 200,
                height: 200,
            },
            enforceBoundary: false,
            enableExif: true
        });
        $('#cropImagePop').on('shown.bs.modal', function() {
            // alert('Shown pop');
            $uploadCrop.croppie('bind', {
                url: rawImg
            }).then(function() {
                console.log('jQuery bind complete');
            });
        });

        $('.item-img').on('change', function() {
            imageId = $(this).data('id');
            tempFilename = $(this).val();
            $('#cancelCropBtn').data('id', imageId);
            readFile(this);
        });
        $('#cropImageBtn').on('click', function(ev) {
            $uploadCrop.croppie('result', {
                type: 'base64',
                format: 'jpeg',
                size: {
                    width: 200,
                    height: 200
                }
            }).then(function(resp) {
                $('#item-img-output').attr('src', resp);
                $('#cropImagePop').modal('hide');

                uploadImg(resp);
            });
        });

        function uploadImg(img) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });

            var Fdata = new FormData();
            Fdata.append('_token', CSRF_TOKEN);
            Fdata.append('img', img);

            $.ajax({
                url: "{{ route('useredit-subimage') }}",
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST', // For jQuery < 1.9
                data: Fdata,
                success: function(result) {
                    console.log(result);
                }
            });
        }
        // End upload preview image
    })
</script>

<style>
    label.cabinet {
        display: block;
        cursor: pointer;
    }

    label.cabinet input.file {
        position: relative;
        height: 100%;
        width: auto;
        opacity: 0;
        -moz-opacity: 0;
        filter: progid:DXImageTransform.Microsoft.Alpha(opacity=0);
        margin-top: -30px;
    }

    #upload-demo {
        width: 250px;
        height: 250px;
        padding-bottom: 25px;
    }

    figure figcaption {
        position: absolute;
        bottom: 0;
        color: #fff;
        width: 100%;
        padding-left: 9px;
        padding-bottom: 5px;
        text-shadow: 0 0 10px #000;
    }
</style>

<div class="global-container">
    <div class="row mt-2">
        <div class="col-12 col-md-8 ml-md-auto mr-md-auto">
            <div class="card">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('useredit') }}">Basic Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('useredit_acc') }}">Account</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Billing</a>
                    </li>
                </ul>

                <div class="card-body">
                    <div class="container">
                        <div class="lead">
                            <h3 class="text-secondary">
                                Update Your Profile
                            </h3>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="cabinet center-block">
                                    <figure class="justify-content-center align-items-center">
                                        <img src="" style="max-height: 100%; height: auto; width: 50%" class="gambar img-fluid rounded-circle" id="item-img-output" />
                                        <!--<figcaption><i class="fa fa-camera"></i></figcaption>-->
                                    </figure>
                                    <input type="file" class="item-img file center-block" name="file_photo" />
                                </label>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('usereditsubmit') }}" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}

                                    <div class="row mt-1">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="form-horizontal px-3 py-1">
                                                    <div class="form-group">
                                                        <label for="invite_people">
                                                            Bio
                                                        </label>
                                                        <div class="col-12 biofield">
                                                            <input placeholder="Bio" class="form-control" type="text" value="{{ users::getbio($user->username) }}" name="bio" id="bio">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-1">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-outline-secondary">Update Info</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cropImagePop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    Crop
                </h4>
            </div>
            <div class="modal-body">
                <div id="upload-demo" class="center-block"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="cropImageBtn" class="btn btn-primary">Crop</button>
            </div>
        </div>
    </div>
</div>

@endsection