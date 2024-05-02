@extends('layouts.app')

@section('title', 'Product Table')

@push('style')
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.6/css/jquery.dataTables.min.css">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Product Table</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <button type="button" class="btn btn-primary mb-3" data-toggle="modal"
                            data-target="#createProductModal">
                            <i class="fa-solid fa-plus" style="color: #ffffff;"></i> Create Product
                        </button>

                        <div class="table-responsive">
                            <table id="products-table" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ 'Rp ' . number_format($product->price, 0, ',', '.') }}</td>
                                            <td>{{ $product->stock }}</td>
                                            <td>{!! $product->status ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>' !!}</td>
                        
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary btn-sm mr-2" title="View Detail">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-warning btn-sm edit-product mr-2"
                                                        data-toggle="modal" data-target="#editProductModal{{ $product->id }}"
                                                        data-product-id="{{ $product->id }}" data-backdrop="false">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    
                                                    <form id="delete-form-{{ $product->id }}"
                                                        action="{{ route('products.destroy', $product->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm delete-product"
                                                            onclick="deleteProduct(event, {{ $product->id }})">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal for creating product -->
    <div class="modal fade" id="createProductModal" tabindex="-1" role="dialog" aria-labelledby="createProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProductModalLabel">Create Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" name="image" id="image" class="form-control-file" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="text" name="price" id="price" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="text" name="stock" id="stock" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals for editing products -->
    @foreach ($products as $product)
        <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editProductModalLabel{{ $product->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel{{ $product->id }}">Edit Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $product->name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control">{{ $product->description }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="image">Image</label>
                                <input type="file" name="image" id="image" class="form-control-file">
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="text" name="price" id="price" class="form-control" value="{{ $product->price }}" required>
                            </div>
                            <div class="form-group">
                                <label for="stock">Stock</label>
                                <input type="text" name="stock" id="stock" class="form-control" value="{{ $product->stock }}" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
    <!-- Include SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>

    <script>
        function deleteProduct(event, productId) {
            event.preventDefault(); // Prevent default form submission
    
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this product!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the delete form
                    $('#delete-form-' + productId).submit();
                }
            });
        }
    
        $(document).ready(function() {
            // Function to handle displaying Sweet Alert for errors
            function showErrorAlert(errorMessage) {
                Swal.fire({
                    title: 'Error!',
                    html: errorMessage,
                    icon: 'error'
                });
            }
    
            // Handle success message after deletion
            var successMessage = '{{ session('success') }}';
            if (successMessage) {
                Swal.fire({
                    title: 'Success!',
                    text: successMessage,
                    icon: 'success',
                    timer: 2000 // Set the timer for auto-close
                });
            }
    
            // Ajax setup for handling errors
            $.ajaxSetup({
                error: function(xhr) {
                    var errorMessage = '';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // If there are validation errors, display validation error message
                        var errors = xhr.responseJSON.errors;
                        errorMessage = '<ul>';
                        $.each(errors, function(key, value) {
                            errorMessage += '<li>' + value + '</li>';
                        });
                        errorMessage += '</ul>';
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        // If there is an error message, display it
                        errorMessage = xhr.responseJSON.error;
                    } else {
                        // If no defined error message, display a generic error message
                        errorMessage = 'An error occurred. Please try again later.';
                    }
                    // Show the error message using Sweet Alert
                    showErrorAlert(errorMessage);
                }
            });
        });
    </script>
@endpush
