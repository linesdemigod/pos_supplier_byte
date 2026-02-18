 <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h1 class="modal-title fs-5" id="exampleModalLabel">Add Customer</h1>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <p class="fw-bold text-success customer-success-msg"></p>
                 <form id="customer-form" method="Post">
                     @csrf
                     <div class="row mb-2">
                         <div class="col">
                             <label for="name" class="form-label">Name</label>
                             <input type="text" name="name" class="form-control">
                             <span class="text-danger name-error"></span>
                         </div>
                     </div>
                     <div class="row mb-2">
                         <div class="col">
                             <div class="">
                                 <div class="form-group mb-3">
                                     <label for="phone" class="form-label">Phone</label>
                                     <input type="number" class="form-control" name="phone" id="phone">
                                     <span class="text-danger phone-error"></span>
                                 </div>
                             </div>
                         </div>

                     </div>
                     <div class="row mb-3">
                         <div class="col">
                             <label for="address" class="form-label">Location</label>
                             <input name="location" type="text" class="form-control" id="location">
                             {{-- <textarea name="address" id="" cols="3" rows="3" class="form-control"></textarea> --}}
                             <span class="text-danger address-error"></span>
                         </div>
                     </div>
                     <div class="form-group mb-3 mt-3">
                         <button type="submit" name="submit" class="btn btn-primary btn-block">Submit</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>
