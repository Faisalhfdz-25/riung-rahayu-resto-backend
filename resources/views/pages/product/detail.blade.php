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
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <span class="label">Description:</span>
                                        <span class="value">{{ $product->description }}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="label">Price:</span>
                                        <span class="value">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="label">Stock:</span>
                                        <span class="value">{{ $product->stock }}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="label">Status:</span>
                                        <span class="value">{!! $product->status ? '<span class="badge badge-success">Ready</span>' : '<span class="badge badge-secondary">Habis</span>' !!}</span>
                                    </li>
                                </ul>
                            </div>
                            
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
