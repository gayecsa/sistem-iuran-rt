@php
    $currentUser = Auth::user();
@endphp

@if($currentUser && ($currentUser->isRt() || $currentUser->role === 'rt'))
    @include('dashboard.rt')
@elseif($currentUser && ($currentUser->isWarga() || $currentUser->role === 'warga'))
    @include('dashboard.warga')
@else
    @include('dashboard.rw')
@endif
