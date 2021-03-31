<div class="card pshort_card">
    <div class="card-img-block">
        <img class="img-fluid" src="{{ config('app.url') }}/images/bg4.png" alt="Card image cap">
    </div>
    <div class="card-body pt-5">
        <img src="{{ config('app.url') }}/user/{{ $username }}/profilepic" alt="profile-image" class="profile" />
        <h5 class="card-title">
            {{ $name }}
            <div class="uname">
                <a href="{{ route('namedprofile',[$username]) }}" style="color: inherit;">{{ "@".$username }}</a>

                <div class="badge badge-secondary">{{ $rating }}</div>

                @if($self)
                <a href="{{ route('useredit') }}">
                    <span class="fas fa-wrench"></span>
                </a>
                @else
                @if($following_flag)
                <a class="px-1 btn btn-outline-secondary btn-sm" role="button" href="{{ route('userunfollow', [$username]) }}">
                    Unfollow
                </a>
                @else
                <a class="px-1 btn btn-outline-secondary btn-sm" role="button" href="{{ route('userfollow', [$username]) }}">
                    Follow
                </a>
                @endif
                @endif
            </div>
        </h5>
        <p class="card-text">
            {{$bio}}
        </p>
        <div class="icon-block">
            <!--<a class="text-success" href="#"><i class="fas fa-brain"></i> {{ $nos_Q }}</a>
            <a class="text-success" href="#"><i class="fas fa-bullhorn"></i> {{ $nos_A }}</a>
            <a class="text-success" href="#"><i class="fas fa-user-shield"></i> {{ $nos_followers }}</a>
            <a class="text-success" href="#"><i class="fas fa-user-shield"></i> {{ $nos_following }}</a>-->
            <div class="container-fluid mb-1">
                <div class="row">
                    <div class="col btn btn-sm">
                        <span class="badge badge-primary-light mr-1">{{ $nos_followers }}</span> Followers
                    </div>
                    <div class="col btn btn-sm">
                        <span class="badge badge-primary-light mr-1">{{ $nos_following }}</span> Following
                    </div>
                </div>
            </div>
        </div>

        <div class="icon-block mt-1">
            <?php foreach ($tags as $tag) { ?>
                <a class="px-1 badge badge-secondary-light" href="{{ config('app.url') }}/tags/{{ $tag }}">{{ $tag }}</a>
            <?php } ?>
        </div>
    </div>
</div>

<style>
    .pshort_card .card-img-block {
        float: left;
        width: 100%;
        height: 150px;
        overflow: hidden;
    }

    .pshort_card .card-body {
        position: relative;
    }

    .pshort_card .profile {
        border-radius: 50%;
        position: absolute;
        top: -42px;
        left: 15%;
        max-width: 75px;
        border: 3px solid rgba(255, 255, 255, 1);
        -webkit-transform: translate(-50%, 0%);
        transform: translate(-50%, 0%);
    }

    .pshort_card h5 {
        font-weight: 600;
        color: #6ab04c;
    }

    .pshort_card .card-text {
        font-weight: 300;
        font-size: 15px;
    }

    .uname {
        color: #90a4ae;
    }

    .pshort_card .icon-block {
        float: left;
        width: 100%;
    }

    .pshort_card .icon-block a {
        text-decoration: none;
    }

    .pshort_card i {
        display: inline-block;
        font-size: 16px;
        color: #6ab04c;
        text-align: center;
        border: 1px solid #6ab04c;
        width: 30px;
        height: 30px;
        line-height: 30px;
        border-radius: 50%;
        margin: 0 5px;
    }

    .pshort_card i:hover {
        background-color: #6ab04c;
        color: #fff;
    }
</style>