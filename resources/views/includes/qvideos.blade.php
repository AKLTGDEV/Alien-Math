@if(count($videos) > 0)
<link href="https://vjs.zencdn.net/7.11.4/video-js.css" rel="stylesheet" />
<script src="https://vjs.zencdn.net/7.11.4/video.min.js"></script>

<div class="row">

    @foreach($videos as $video)
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Video <b>#{{ $video->id }} ({{ $video->filename }})</b>
            </div>
            <div class="card-body">
                <video id="video-{{ $video->id }}" class="video-js" controls preload="auto" style="width: 100%;" data-setup="{}">
                    <source src="{{ route('video.stream', [$video->id]) }}" type="video/mp4" />
                    <p class="vjs-no-js">
                        To view this video please enable JavaScript, and consider upgrading to a
                        web browser that
                        <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                    </p>
                </video>
            </div>
            <div class="card-footer">
                <a href='{{ route("video.detach.".$type, [$video->id, $question->id]) }}' class="btn btn-sm btn-danger">
                    Detach
                </a>
            </div>
        </div>
    </div>
    @endforeach

</div>
@endif