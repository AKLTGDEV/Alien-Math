@extends('layouts.app')

@section('content')

<script src="{{ asset('thirdparty/jquery-ui.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('thirdparty/jquery-ui.css') }}" />
<script src="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.js') }}"></script>
<link rel="stylesheet" href="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.css') }}" />

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js" integrity="sha512-Gs+PsXsGkmr+15rqObPJbenQ2wB3qYvTHuJO6YJzPe/dTLvhy0fmae2BcnaozxDo5iaF8emzmCZWbQ1XXiX2Ig==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" integrity="sha512-zxBiDORGDEAYDdKLuYU9X/JaJo/DPzE42UubfBw9yg8Qvb2YRRIQ8v4KsGHOx2H1/+sdSXyXxLXv5r7tHc9ygg==" crossorigin="anonymous" />-->

<script src="{{ asset('thirdparty/croppie/croppie.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('thirdparty/croppie/croppie.min.css') }}" />

<?php

use App\users;
use Illuminate\Support\Facades\Auth;


$name = $user->name;
$username = $user->username;
$bio = users::getbio($username);
$tags = json_decode(users::gettags($user->username), true);
$nos_Q = $user->nos_Q;
$nos_A = $user->nos_A;
$nos_followers = $user->nos_followers;
$nos_following = $user->nos_following;
$rating = $user->rating;

$me = Auth::user();

if ($me->username == $username) {
    $self_flag = true;
} else {
    $self_flag = false;
}

$self_following = json_decode($me->following);
if (in_array($user->id, $self_following)) {
    $following_flag = true;
} else {
    $following_flag = false;
}

?>

<script type="text/javascript">
    jQuery(document).ready(function() {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var tags_src = JSON.parse('<?php echo json_encode($tags_suggested); ?>');

        $("#tags_h").click(function(e) {
            $("#TagsModal").modal('show');
        });

        $("#tags-done").click(function(e) {
            //console.log("Done")
            var tag_selections = [];

            for (let k = 0; k < tags_src.length; k++) {
                //console.log($("#tag-"+k).is(':checked'));
                if ($("#tag-" + k).is(':checked')) {
                    tag_selections.push(tags_src[k]);
                }
            }

            tag_selections.forEach(T => {
                $("#tags").addTag(T)
            });

            $("#TagsModal").modal('hide')

        })

        @if($newuser)

        $("#NewUserModal").modal('show')

        $("#footer-proceed").click(function(e) {
            $("#NewUserModal").modal('hide')
            $("#TagsModal").modal('show');
        });

        @endif


        $('#tags').tagsInput({
            'interactive': true,
            'autocomplete': {
                source: tags_src
            },
            'unique': true
        });

        var image_crop = $('#image_demo').croppie({
            viewport: {
                width: 300,
                height: 300,
                type: 'square'
            },
            boundary: {
                width: 650,
                height: 350
            }
        });
        $('#cover_image').on('change', function() {
            var reader = new FileReader();
            reader.onload = function(event) {
                image_crop.croppie('bind', {
                    url: event.target.result,
                });
            }
            reader.readAsDataURL(this.files[0]);
            $('#uploadimageModal').modal('show');
        });


        /// Get button click event and get the current crop image
        $('.crop_image').click(function(event) {
            var formData = new FormData();
            image_crop.croppie('result', {
                type: 'base64',
                format: 'jpeg',
                //format: 'png',
                size: {
                    width: 150,
                    height: 150
                }
            }).then(function(data) {
                uploadImg(data);
            });
            $('#uploadimageModal').modal('hide');
        }); /// Ajax Function

        function uploadImg(img) {
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
    })
</script>

<style>
    /* Important part */
    .modal-dialog {
        overflow-y: initial !important
    }

    .modal-body {
        height: 250px;
        overflow-y: auto;
    }

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

<div class="container main">
    <div class="row">
        <div class="col-12">
            <div class="card mt-2">
                <div class="card-title mt-1 px-2">
                    <div class="d-sm-flex justify-content-between align-items-center mb-2">
                        <h3 class="text-dark mb-0">Edit Profile</h3>
                    </div>
                </div>
                <div class="card-body">
                    @if ( count( $errors ) > 0 )
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                        @endforeach
                    </div>
                    @endif
                    <form action="{{ route('usereditsubmit') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="form-horizontal px-3 py-1">
                                        <div class="form-group">
                                            <label class="control-label">First Name</label>
                                            <div class="">
                                                <input value="{{ $ext['fname'] }}" class="form-control" type="text" name="fname" placeholder="First Name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Last Name</label>
                                            <div class="">
                                                <input value="{{ $ext['lname'] }}" class="form-control" type="text" name="lname" placeholder="Last Name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Email</label>
                                            <div class="">
                                                <input value="{{ $user->email }}" class="form-control" type="email" name="email" placeholder="Email" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Username</label>
                                            <div class="">
                                                <input value="{{ $user->username }}" class="form-control" type="text" name="username" placeholder="Username" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Password</label>
                                            <div class="">
                                                <input class="form-control" type="password" name="password" placeholder="Password" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="form-horizontal px-3 py-1">
                                                <div class="form-group">
                                                    <label for="tags">
                                                        Tags
                                                    </label>
                                                    <div class="col-12 bootstrap-tagsinput" id="tags_h">
                                                        <input class="form-control" type="text" value="{{ implode(',' , json_decode(users::gettags($user->username))) }}" name="tags" id="tags">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                    <div class="col-lg-12 col-md-12 text-center">
                                        <h4>Profile Pic</h4>
                                    </div>
                                    <div class="col-12">
                                        <div class="jumbotron text-center">
                                            <div class="row">
                                                <div class="col-12">
                                                    <img class="img-fluid" id="uploaded-image" src="{{ config('app.url') }}/user/{{ $username }}/profilepic" height="100%">
                                                </div>
                                                <div class="input-group mt-3">
                                                    <div class="custom-file">
                                                        <input type="file" accept="image/*" id="cover_image">
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
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- This is the modal -->
<div class="modal" tabindex="-1" role="dialog" id="uploadimageModal">
    <div class="modal-dialog" role="document" style="min-width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="image_demo"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary crop_image">Crop and Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Important part */
    .modal-dialog {
        overflow-y: initial !important
    }

    .modal-body {
        height: 250px;
        overflow-y: auto;
    }

    .searchable-container label.btn-default.active {
        background-color: #007ba7;
        color: #FFF
    }

    .searchable-container label.btn-default {
        width: 100%;
        border: 1px solid #efefef;


    }

    .searchable-container label .bizcontent {
        width: 100%;
    }

    .searchable-container .btn-group {
        width: 100%;
    }

    .searchable-container .btn span.glyphicon {
        opacity: 0;
    }

    .searchable-container .btn.active span.glyphicon {
        opacity: 1;
    }
</style>

<div class="modal shadow" id="TagsModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">

                <p class="text-center text-info">
                    Select Tags
                </p>

                <div class="container-fluid searchable-container">
                    <div class="row">
                        <?php
                        $i = 0;
                        foreach ($tags_suggested as $t) {
                            ?>
                            <div class="col items">
                                <div class="info-block block-info clearfix">
                                    <div class="square-box pull-left">
                                        <span class="glyphicon glyphicon-tags glyphicon-lg"></span>
                                    </div>
                                    <div data-toggle="buttons" class="btn-group bizmoduleselect">
                                        <label class="btn btn-default">
                                            <div class="bizcontent">
                                                <input type="checkbox" id="tag-{{ $i }}" name="" autocomplete="off">
                                                <span class="glyphicon glyphicon-ok glyphicon-lg"></span>
                                                <h5>
                                                    {{ $t }}
                                                </h5>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php $i++;
                        } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="tags-done" type="button" class="btn btn-primary">Finish</button>
            </div>
        </div>
    </div>
</div>

@if ($newuser)

<div class="modal" id="NewUserModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="lead text-center text-secondary mb-2">
                    Welcome to CrowDoubt !
                </div>

                <p class="text-center text-info">
                    Hello, {{ $ext['fname'] }} &#128517; <br>
                    CrowDoubt is a place for teachers and students to collaborate, have classes, & create and attempt question papers. Online. For free.
                </p>

                <p class="text-center text-info">
                    <span class="text-center text-info" style="display: block">
                        Get started by entering your Bio and chosing your `tags`. Most popular tags By your location are <span class="badge badge-primary">JEE</span> and <span class="badge badge-primary">NEET</span> .
                    </span>
                </p>
            </div>

            <div class="modal-footer">
                <button id="footer-proceed" type="button" class="btn btn-primary">Proceed</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

@endif

@endsection