@extends('layout.layout')

@section('title', 'Item Summary')

@section('content')
    <x-breadcrumb title="App" subtitle='Report' name='Item Summary' href="report.index" />

    <div class="">
        <button class="btn btn-success action-icon text-white" id="exportBtn">
            <i class="fa fas-download"></i>
            Export
        </button>

        <livewire:report-sale-item-summary />
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('exportBtn').addEventListener('click', function() {

            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            window.location.href = `/report/export-item-summary?start=${startDate}&end=${endDate}`;
        });
    </script>
@endpush
