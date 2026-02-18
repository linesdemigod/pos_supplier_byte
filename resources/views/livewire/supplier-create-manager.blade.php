  <div class="row">
      <div class="col-xl-5 col-lg-5">
          <div class="card">
              <div class="card-body">

                  <h4 class="card-title mb-3">
                      Suppliers
                  </h4>
                  <form wire:submit.prevent="submitAdd">

                      <div class="mb-3">
                          <label for="addName" class="form-label">Name</label>
                          <input type="text" wire:model="addName" class="form-control" placeholder="Name">
                          @error('addName')
                              <small class="text-danger">{{ $message }}</small>
                          @enderror
                      </div>
                      <div class="mb-3">
                          <label for="addContactInfo" class="form-label">Contact Info</label>
                          <input type="text" wire:model="addContactInfo" class="form-control"
                              placeholder="ContactInfo">
                          @error('addContactInfo')
                              <small class="text-danger">{{ $message }}</small>
                          @enderror
                      </div>
                      <div class="mb-3">
                          <label for="addAddress" class="form-label">Address</label>
                          <input type="text" wire:model="addAddress" class="form-control" placeholder="Address">
                          @error('addAddress')
                              <small class="text-danger">{{ $message }}</small>
                          @enderror
                      </div>
                      <div class="mb-3">
                          <label for="addEmail" class="form-label">Email</label>
                          <input type="text" wire:model="addEmail" class="form-control" placeholder="Email">
                          @error('addEmail')
                              <small class="text-danger">{{ $message }}</small>
                          @enderror
                      </div>

                      <button type="submit" class="btn btn-primary">Add</button>
                  </form>
              </div>
          </div>
      </div>

      {{-- table --}}
      <div class="col-xl-7 col-lg-7">

          <div class="card">
              <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center gap-5">
                      {{-- create show perpage --}}
                      <div class="d-flex align-items-center gap-2">
                          <label for="per_page">Show:</label>
                          <select wire:model.live="perPage" class="form-select-sm form-select">
                              <option value="10">10</option>
                              <option value="25">25</option>
                              <option value="50">50</option>
                              <option value="100">100</option>
                          </select>
                      </div>
                      {{-- create search input --}}
                      <div class="d-flex align-items-center gap-2">
                          <label for="search">Search:</label>
                          <input wire:model.live.debounce.300ms="search" type="search" id="search" name="search"
                              class="form-control" placeholder="search by name, contact info..." />
                      </div>
                  </div>
                  <div class="table-responsive">

                      <x-flash-message />
                      @unless (count($suppliers) == 0)
                          <table class="table-striped table">
                              <thead>
                                  <tr>
                                      <th scope="col">S/N</th>
                                      <th scope="col">Name</th>
                                      <th scope="col">Contact Info</th>
                                      <th scope="col">Address</th>
                                      <th scope="col">Action</th>
                                  </tr>
                              </thead>
                              <tbody id="table-body">
                                  @foreach ($suppliers as $key => $supplier)
                                      <tr>

                                          <td> {{ $key + 1 }} </td>
                                          <td> {{ Str::title($supplier->name) }} <br> <span
                                                  class="text-primary">{{ $supplier->email ?? '' }}</span>
                                          </td>
                                          <td> {{ $supplier->contact_info }} </td>
                                          <td> {{ Str::title($supplier->address) }} </td>
                                          <td>
                                              <div class="d-flex align-items-center justify-start gap-3">
                                                  {{-- @can('supplier.edit') --}}
                                                  <button wire:click="edit({{ $supplier->id }})"
                                                      class="btn btn-warning action-icon text-white" data-bs-toggle="modal"
                                                      data-bs-target="#editModal">
                                                      <i class="fas fa-edit"></i>
                                                  </button>
                                                  {{-- @endcan
                                                  @can('supplier.delete') --}}
                                                  <button onclick="confirmDelete({{ $supplier->id }})"
                                                      class="btn btn-danger action-icon text-white">
                                                      <i class="fas fa-trash"></i>
                                                  </button>
                                                  {{-- @endcan --}}

                                              </div>
                                          </td>
                                      </tr>
                                  @endforeach
                              </tbody>
                          </table>
                      @else
                          <h3 class="text-center">No record found</h3>
                      @endunless

                  </div>
                  <div class="mt-3">
                      {{ $suppliers->links() }}
                  </div>
              </div>
          </div>
      </div>

      <!-- Edit Subject Modal -->
      <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel"
          aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">Edit Supplier</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <form wire:submit.prevent="submitEdit">
                      <div class="modal-body">
                          <div class="mb-3">
                              <label for="editName" class="form-label">Name</label>
                              <input type="text" wire:model="editName" class="form-control">
                              @error('editName')
                                  <small class="text-danger">{{ $message }}</small>
                              @enderror
                          </div>

                          <div class="mb-3">
                              <label for="editContactInfo" class="form-label">Contact Info</label>
                              <input type="text" wire:model="editContactInfo" class="form-control"
                                  placeholder="ContactInfo">
                              @error('editContactInfo')
                                  <small class="text-danger">{{ $message }}</small>
                              @enderror
                          </div>
                          <div class="mb-3">
                              <label for="editAddress" class="form-label">Address</label>
                              <input type="text" wire:model="editAddress" class="form-control"
                                  placeholder="Address">
                              @error('editAddress')
                                  <small class="text-danger">{{ $message }}</small>
                              @enderror
                          </div>
                          <div class="mb-3">
                              <label for="editEmail" class="form-label">Email</label>
                              <input type="text" wire:model="editEmail" class="form-control" placeholder="Email">
                              @error('editEmail')
                                  <small class="text-danger">{{ $message }}</small>
                              @enderror
                          </div>

                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-primary">Update</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
  @push('scripts')
      <script>
          document.addEventListener('livewire:init', function() {
              Livewire.on('close-modal', () => {
                  const modalElement = document.getElementById('editModal');
                  // Check if the modal is currently open
                  if (modalElement) {
                      const modalInstance = bootstrap.Modal.getInstance(modalElement);

                      // If the modal instance exists, hide it
                      if (modalInstance) {
                          modalInstance.hide();
                      } else {
                          // If there's no instance, initialize and hide the modal
                          const modal = new bootstrap.Modal(modalElement);
                          modal.hide();
                      }
                  }


              });
          });

          document.addEventListener('livewire:initialized', function() {
              Livewire.on('formSubmitted', (data) => {
                  notyf.success(data.message)
              });
          });

          function confirmDelete(id) {
              Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, delete it!'
              }).then((result) => {
                  if (result.isConfirmed) {

                      Livewire.dispatch('deleteSupplier', {
                          supplier: id
                      });

                  }
              });
          }
      </script>
  @endpush
