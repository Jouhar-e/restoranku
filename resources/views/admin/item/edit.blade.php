@extends('admin.layouts.master')

@section('title', 'Edit Menu')

@section('content')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Data Menu</h3>
                <p class="text-subtitle text-muted">Silakan input data menu.</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">Edit Error!</h5>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form action="{{ route('items.update', $item->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Nama Menu</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    placeholder="Masukkan Menu" value="{{ $item->name }}" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea type="text" class="form-control" name="description" id="description" placeholder="Masukkan Deskripsi"
                                    required>{{ $item->description }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="harga">Harga</label>
                                <input type="number" class="form-control" name="price" id="harga"
                                    placeholder="Masukkan Harga" value="{{ $item->price }}" required>
                            </div>

                            <div class="form-group">
                                <label for="category">Kategori</label>
                                <select class="form-select" name="category_id" id="category">
                                    <option value="" disabled>Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->cat_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="img">Gambar</label>
                                @if ($item->img)
                                    <div class="mt-2 mb-2">
                                        <img src="{{ asset('img_item_upload/' . $item->img) }}" alt="{{ $item->img }}"
                                            class="img-thumbnail mb-2" style="width: 200px; height: 100px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="img" id="img">
                            </div>

                            <div class="form-group">
                                <label for="is_active">Status</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" class="form-check-input" name="is_active"
                                        id="flexSwitchCheckDisabled" value="1"
                                        {{ $item->is_active == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flexSwitchCheckChecked">Aktif/Tidak Aktif</label>
                                </div>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
                                <button type="submit" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                                <a href="{{ route('items.index') }}" class="btn btn-danger me-1 mb-1">Batal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
