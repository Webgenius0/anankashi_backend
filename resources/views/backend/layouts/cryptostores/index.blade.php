@extends('backend.app', ['title' => 'Crypto Stores'])

@push('styles')
<link href="{{ asset('default/datatable.css') }}" rel="stylesheet" />
<style>
    .star-rating { color: #f5a623; }
    .badge-tax { background-color: #0d6efd; }
    .badge-legal { background-color: #198754; }
    .badge-crypto { background-color: #6f42c1; }
</style>
@endpush

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            <!-- PAGE-HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Crypto Stores</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ url('admin/dashboard') }}"><i class="fe fe-home me-2 fs-14"></i>Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Crypto Stores</li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- ROW -->
            <div class="row">
                <div class="col-12">
                    <div class="card product-sales-main">
                        <div class="card-header border-bottom">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-danger"><a href="#" style="color:#fff;text-decoration:none;">Import</a></button>
                                <button type="button" class="btn btn-warning"><a href="#" style="color:#fff;text-decoration:none;">Export</a></button>
                            </div>
                            <div class="card-options ms-auto">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                                    <i class="fe fe-plus me-1"></i> Add Crypto Store
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered text-nowrap border-bottom" id="datatable">
                                <thead>
                                    <tr>
                                        <th class="bg-transparent border-bottom-0">#</th>
                                        <th class="bg-transparent border-bottom-0">Image</th>
                                        <th class="bg-transparent border-bottom-0">Name</th>
                                        <th class="bg-transparent border-bottom-0">Title</th>
                                        <th class="bg-transparent border-bottom-0">Type</th>
                                        <th class="bg-transparent border-bottom-0">Rating</th>
                                        <th class="bg-transparent border-bottom-0">Reviews</th>
                                        <th class="bg-transparent border-bottom-0">Created</th>
                                        <th class="bg-transparent border-bottom-0">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ROW END -->

        </div>
    </div>
</div>

<!-- ===================== CREATE MODAL ===================== -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.crypto-stores.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel"><i class="fe fe-plus-circle me-2"></i>Add Crypto Store</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Enter title" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="" disabled selected>Select type</option>
                                <option value="Tax_advisors">Tax Advisors</option>
                                <option value="Legal_advisors">Legal Advisors</option>
                                <option value="Crypto_partners">Crypto Partners</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rating</label>
                            <input type="number" name="rating" class="form-control" placeholder="0 - 5" min="0" max="5" step="0.1">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/jpg,image/jpeg,image/png,image/webp" onchange="previewImage(this, 'createPreview')">
                            <div class="mt-2">
                                <img id="createPreview" src="#" alt="Preview" class="img-thumbnail d-none" style="height:80px;">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Review</label>
                            <input type="text" name="review" class="form-control"  placeholder="Enter review..."></input>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fe fe-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================== EDIT MODAL ===================== -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel"><i class="fe fe-edit me-2"></i>Edit Crypto Store</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" id="edit_type" class="form-select" required>
                                <option value="Tax_advisors">Tax Advisors</option>
                                <option value="Legal_advisors">Legal Advisors</option>
                                <option value="Crypto_partners">Crypto Partners</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rating</label>
                            <input type="number" name="rating" id="edit_rating" class="form-control" min="0" max="5" step="0.1">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/jpg,image/jpeg,image/png,image/webp" onchange="previewImage(this, 'editPreview')">
                            <div class="mt-2">
                                <img id="editPreview" src="#" alt="Current Image" class="img-thumbnail" style="height:80px;">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Review</label>
                            <textarea name="review" id="edit_review" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fe fe-save me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================== DELETE MODAL ===================== -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <div class="mb-3">
                        <span class="avatar avatar-xl bg-danger-transparent rounded-circle">
                            <i class="fe fe-trash-2 fs-30 text-danger"></i>
                        </span>
                    </div>
                    <h5 class="fw-semibold">Delete Crypto Store</h5>
                    <p class="text-muted fs-13">Are you sure you want to delete <strong id="deleteStoreName"></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4"><i class="fe fe-trash me-1"></i>Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('default/datatable.js') }}"></script>
<script>
    // ── DataTable Init ──
    $(document).ready(function () {
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.crypto-stores.index') }}',
            columns: [
                { data: 'id',         name: 'id' },
                { data: 'image',      name: 'image',      orderable: false, searchable: false },
                { data: 'name',       name: 'name' },
                { data: 'title',      name: 'title' },
                { data: 'type',       name: 'type' },
                { data: 'rating',     name: 'rating' },
                { data: 'review',     name: 'review' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action',     name: 'action',     orderable: false, searchable: false },
            ],
        });
    });

    // ── Image Preview ──
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ── Edit Modal ──
    function openEditModal(id, name, title, type, rating, review, imageUrl) {
        document.getElementById('editForm').action = '/admin/crypto-stores/update/' + id;
        document.getElementById('edit_name').value   = name;
        document.getElementById('edit_title').value  = title;
        document.getElementById('edit_type').value   = type;
        document.getElementById('edit_rating').value = rating ?? '';
        document.getElementById('edit_review').value = review ?? '';

        const preview = document.getElementById('editPreview');
        if (imageUrl) {
            preview.src = imageUrl;
            preview.classList.remove('d-none');
        } else {
            preview.classList.add('d-none');
        }

        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    // ── Delete Modal ──
    function openDeleteModal(id, name) {
        document.getElementById('deleteForm').action = '/admin/crypto-stores/delete/' + id;
        document.getElementById('deleteStoreName').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // ── Flash message auto-dismiss ──
    @if(session('success'))
        setTimeout(() => {
            const alert = document.querySelector('.alert-success');
            if (alert) alert.remove();
        }, 4000);
    @endif
</script>
@endpush
