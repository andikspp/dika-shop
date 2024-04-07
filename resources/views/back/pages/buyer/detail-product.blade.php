@extends('back.layout.pages-layout')

@section('title', $product->name)

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <main>
        <!-- Product Detail Section -->
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-6">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <h2>{{ $product->name }}</h2>
                    <p>Description: {{ $product->description }}</p>
                    <p>Price: Rp {{ $product->price }}</p>
                    <p>Stock: {{ $product->stock }}</p>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <form action="{{ route('cart.add') }}" method="post" class="d-flex align-items-center">
                                    @csrf
                                    <div class="input-group">
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="number" name="quantity" class="form-control" value="1"
                                            min="1" required>

                                        <button type="submit" class="btn btn-primary text-white">Add to Cart</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 col-sm-12 mt-3 mt-md-0">
                                <a href="{{ route('buyer.home') }}#produk" class="btn btn-danger text-white">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection
