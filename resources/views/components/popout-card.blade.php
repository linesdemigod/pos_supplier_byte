@props(['title', 'message', 'btnText', 'btnColor', 'id'])

<div class="custom-modal" id="myModal">
    <div class="modal-content">
        @if ($id != 2)
            <span class="close" id="closeModal">&times;</span>
        @endif
        <h2>{{ $title }}</h2>
        <p>{{ $message }}</p>
        <div class="d-flex justify-content-evenly gap-3">
            {{-- show cancel button if id is 2 --}}
            @if ($id == 2)
                <button class="form-control btn btn-secondary cancel-day" data-id={{ $id }}>Cancel</button>
            @endif
            <button class="form-control btn {{ $btnColor }} start-day"
                data-id={{ $id }}>{{ $btnText }}</button>
        </div>
    </div>
</div>
