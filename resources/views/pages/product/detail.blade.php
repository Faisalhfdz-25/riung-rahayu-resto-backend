@extends('layouts.app')

@section('title', 'Product Detail')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Product Detail</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid">
                                <form action="{{ route('products.change-image', $product->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group mt-2">
                                        <input type="file" name="image" id="image" class="form-control-file">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Change Image</button>
                                </form>
                            </div>
                            <div class="col-md-8">
                                <h3>{{ $product->name }}</h3>
                                <p>{{ $product->description }}</p>
                                <p><strong>Price:</strong> Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p><strong>Stock:</strong> {{ $product->stock }}</p>
                                <p><strong>Status:</strong> {!! $product->status ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>' !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
