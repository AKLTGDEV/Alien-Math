<div class="row">
    <?php
    $opt_nos = $post['opt_nos'];
    for ($i = 1; $i <= $opt_nos; $i++) { ?>

        <div class="col-12 mt-sm-1">
            <div style="width: 100%; overflow: auto;" class="option btn 
        
        @if ($given == $i)
            @if ($corr == $i)
                {{ 'btn-success' }}
            @else
                {{ 'btn-danger' }}
            @endif
        @else
            {{ 'btn-outline-primary btn-rounded waves-effect' }}
        @endif

        " id="opt{{ $i }}_{{ $post['pid'] }}" pid="{{ $post['pid'] }}" opt="{{ $i }}">
                {{ $post['options'][$i-1] }}
            </div>
        </div>
    <?php } ?>
</div>