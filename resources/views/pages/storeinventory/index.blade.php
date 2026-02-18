@extends('layout.layout')

@section('title')
    {{ 'Store Inventory' }}
@endsection
@section('page-id', 'store_inventory_index')



@section('content')
    <x-breadcrumb title="Create Store Inventory" subtitle='Store Inventory' name='Store Inventory'
        href='storeinventory.create' />

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">
                @can('storeInventory.create')
                    <a href="{{ route('storeinventory.create') }}" class="btn btn-primary">
                        <i class="uil-plus"></i>
                        Create Store Inventory</a>
                @endcan


                <div class="d-flex justify-content-end gap-3">
                    @can('storeInventory.import')
                        <form enctype="multipart/form-data" class="d-flex justify-content-start flex-nowrap gap-2"
                            id="import-form">
                            @csrf
                            <div class="">

                                <input type="file" name="excel" class="form-control" id="">
                                <small class="text-danger excel-error"></small>

                            </div>

                            <div class="">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i>
                                    Import
                                </button>

                            </div>

                        </form>

                        <a href="" class="btn btn-primary" download>Export</a>
                    @endcan
                </div>



            </div>
            {{-- <x-flash-message /> --}}
            <livewire:store-inventory-table />
        </div>
    </div>
@endsection
@section('script')
    <script>
        // let notify = new Notyf();
        const ImportForm = document.querySelector("#import-form");
        const tableBody = document.querySelector('#table-body');
        ImportForm.addEventListener('submit', importFromExcel)
        const loader = document.querySelector('#loader');


        async function importFromExcel(e) {
            e.preventDefault();
            loader.classList.remove('invisible');

            const formData = new FormData(ImportForm);
            try {
                const res = await axios.post('/store-inventory/excel-import', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        "X-Requessted-With": "XMLHttpRequest",
                    }
                })

                const {
                    success
                } = res.data


                if (res.status === 200) {

                    notyf.success(success)

                    ImportForm.reset(); //clear the forms

                    //refresh page
                    setTimeout(() => {
                        location.reload();
                    }, 3000);

                }



            } catch (error) {
                console.log(error)
                const errors = error?.response?.data.error ?? {};
                // document.querySelector('.excel-error').innerText = errors.excel[0] ?? '';
                console.log(errors)

            } finally {
                loader.classList.add('invisible');
            }
        }
    </script>
@endsection
