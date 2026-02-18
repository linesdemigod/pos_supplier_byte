@props(['name', 'title', 'subtitle', 'href' => 'dashboard', 'id' => ''])

{{-- <div class="page-header"> --}}

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">

    <div class="">
        <h4 class="page-title">{{ $name }}</h4>
    </div>
    <div class="">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route($href, $id) }}">{{ $title }}</a></li>
            <li class="breadcrumb-item active">{{ $subtitle }}</li>
        </ol>
    </div>
</div>
{{-- </div> --}}
