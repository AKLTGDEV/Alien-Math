<div class="row">
    <div class="col-6">
        <div style="width: 100%" class="class_q_option btn 
        
        @if ($given == 1)
            @if ($corr == 1)
                {{ 'btn-success' }}
            @else
                {{ 'btn-danger' }}
            @endif
        @else
            {{ 'btn-outline-primary btn-rounded waves-effect' }}
        @endif

        " id="opt1_{{ $unique_name }}" unq_name="{{ $unique_name }}" opt="1">
            {{ $opts_object[0] }}
        </div>
    </div>
    <div class="col-6">
        <div style="width: 100%" class="class_q_option btn 
        @if ($given == 2)
            @if ($corr == 2)
                {{ 'btn-success' }}
            @else
                {{ 'btn-danger' }}
            @endif
        @else
            {{ 'btn-outline-primary btn-rounded waves-effect' }}
        @endif

        ?>" id="opt2_{{ $unique_name }}" unq_name="{{ $unique_name }}" opt="2">
            {{ $opts_object[1] }}
        </div>
    </div>
</div>