<script>
    @if (session()->has('message'))
        notyf.success("{{ session('message') }}");
    @endif
    @if (session()->has('error'))
        notyf.error("{{ session('error') }}");
    @endif
</script>
