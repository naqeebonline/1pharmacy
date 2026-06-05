<div class="header-elements d-none mb-3 mb-md-0">
    <div class="d-flex justify-content-center">

        @if(isset($back_route))

            @if(isset($back_route[2]))
                <a href="{{ $back_route[0] }}" class="btn btn-warning btn-sm mr-1"><i class="icon-arrow-left16 mr-1"></i>
                    {{ $back_route[1] }}</a>
            @else
                <a href="{{ route($back_route[0]) }}" class="btn btn-warning btn-sm mr-1"><i class="icon-arrow-left16 mr-1"></i>
                    {{ $back_route[1] }}</a>
            @endif

        @endif

        @if(isset($new_route))

            @if(isset($new_route[2]))
                    <a href="{{ $new_route[0] }}" class="btn btn-info btn-sm"><i class="icon-database-add mr-1"></i>
                        {{ $new_route[1] }}</a>
            @else
                    <a href="{{ route($new_route[0]) }}" class="btn btn-info btn-sm"><i class="icon-database-add mr-1"></i>
                        {{ $new_route[1] }}</a>
            @endif


        @endif

    </div>
</div>
