<style>
    .p_short_holder * {
        margin: 0px;
        padding: 0px;
    }

    a {
        text-decoration: none !important;
        color: #333;
    }

    ul,
    ol {
        margin: 0px;
    }

    .p_short {
        width: 95%;
        height: auto;
        background: #32C7EE;
        margin: 0 auto;
        -moz-box-shadow: 0 0 5px #000;
        -webkit-box-shadow: 0 0 5px#000;
        box-shadow: 0 0 5px #000;
        margin-top: 1%;
    }

    .p_short h1 {
        font-size: 24px;
        margin-top: 0px;
    }

    .img-container {
        width: 100%;
        text-align: center;
        padding-top: 5%;
        padding-bottom: 5%;
    }

    .pp__ {
        display: inline-block;
        vertical-align: middle;
        height: 96px;
        width: 96px;
        border-radius: 100%;
        border: 1px solid #F8F8F8;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
    }

    .pp__ .pp-img__ {
        width: 100%;
        height: 100%;
        border-radius: 100%;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        vertical-align: middle;
    }

    .robin-key {
        width: 100%;
        height: auto;
        background: #fff;
        float: left;
    }

    .robin-key p {
        font-size: 15px;
        color: #828282;
        float: left;
        line-height: 42px;
        margin-bottom: 0px;
        margin-left: 20px;
    }

    .robin-key ul {
        float: right;
        margin: 0px;
        padding: 0px;
    }

    .robin-key ul li {
        padding: 4px 12px;
        margin-top: 6px;
        border-right: 1px solid #ddd;
        color: #3f4c6b;
        list-style: none;
        float: left;
    }

    .robin-key ul li a {
        color: #3f4c6b;
    }

    .robin-key ul li a:hover {
        color: #242D3F;
    }

    .robin-key ul li:last-child {
        border-right: none;
    }

    ul.follow-list {
        margin: 0px;
        padding: 0px;
        background: #3f4c6b;
        width: 100%;
    }

    ul.follow-list li {
        width: 25%;
        float: left;
        list-style: none;
        padding: 8px 0px;
        background: #111F28;
    }

    ul.follow-list li a {
        padding: 2px 14px;
        display: inline-block;
        color: #fff;
        font-size: 13px;
        border-right: 1px solid #1C3544;
    }

    ul.follow-list li:last-child a {
        border-right: none;
    }

    .profile-desc-text p {
        padding: 5%;
        background: #111F28;
    }
</style>

<div class="p_short_holder">

    <div class="p_short">
        <div class="img-container">
            <span class="pp__">
                <div class="pp-img__" style="background-image: url({{ config('app.url') }}/user/{{ $username }}/profilepic);">
                </div>
            </span>
        </div>
        <div class="robin-key">
            <p>
                {{ $name }}
            </p>

            <ul>
                @if($self)
                <li>
                    <a href="{{ route('useredit') }}">
                        <span class="fas fa-wrench"></span>
                    </a>
                </li>
                @else
                @if($following_flag)
                <li>
                    <a class="px-1 btn btn-outline-secondary btn-sm d-none d-sm-inline-block" role="button" href="{{ route('userunfollow', [$username]) }}">
                        Unfollow
                    </a>
                </li>
                @else
                <li>
                    <a class="px-1 btn btn-outline-primary btn-sm d-none d-sm-inline-block" role="button" href="{{ route('userfollow', [$username]) }}">
                        Follow
                    </a>
                </li>
                @endif
                @endif
            </ul>
        </div>
        <div style="display: table; margin: 0 auto; width: 100%; text-align: center; background: #111F28; color: #fff; padding: 5px">
            <p style="text-align: center; color: wheat">
                {{$bio}}
            </p>
        </div>

        <ul class="follow-list">
            <li><a href="#"><i class="fas fa-brain"></i> {{ $nos_Q }}</a></li>
            <li><a href="#"><i class="fas fa-bullhorn"></i> {{ $nos_A }}</a></li>
            <li><a href="#"><i class="fas fa-user-shield"></i> {{ $nos_followers }}</a></li>
            <li><a href="#"><i class="fas fa-user-shield"></i> {{ $nos_following }}</a></li>
        </ul>
        <div style="display: table; margin: 0 auto; width: 100%; text-align: center; background: #111F28; color: #fff; padding: 5px">
            <a href="{{ config('app.url') }}/u/{{ $username }}/" style="color: wheat">
                <b>/u/</b>{{ $username }}
            </a>
            <!--<span class="badge badge-pill badge-info" style="padding:4px">{{ $rating }}</span>-->
        </div>
        <div class="profile-desc-links" style="display: table; margin: 0 auto;">
            <div class="user-tags-list" style="margin-top: 3px;margin-bottom: 3px">
                <?php foreach ($tags as $tag) { ?>
                    <a style="text-decoration:none; padding:4px" class="badge badge-secondary" href="{{ config('app.url') }}/tags/{{ $tag }}">{{ $tag }}</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>