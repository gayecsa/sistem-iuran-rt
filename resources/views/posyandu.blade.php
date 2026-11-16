@if(auth()->check() && auth()->user()->isRw())
    @include('posyandu.index_rw')
@elseif(auth()->check() && auth()->user()->isRt())
    @include('posyandu.index_rt')
@else
    @include('posyandu.index_warga')
@endif
