@extends('admin.layouts.master')

@section('title', 'Tambah Karyawan')

@section('content')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Data User</h3>
                <p class="text-subtitle text-muted">Silakan input data user.</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">Submit Error!</h5>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form action="{{ route('users.update', $user->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="fullname">Nama Karyawan</label>
                                <input type="text" class="form-control" name="fullname" id="fullname"
                                    placeholder="Masukkan Nama Karyawan" value="{{ $user->fullname }}" required>
                            </div>

                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" value="{{ $user->username }}" name="username"
                                    id="username" placeholder="Masukkan Username" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" name="email"
                                    id="email" placeholder="Masukkan Email" required>
                            </div>

                            <div class="form-group">
                                <label for="phone">Nomer HP</label>
                                <input type="phone" class="form-control" value="{{ $user->phone }}" name="phone"
                                    id="phone" placeholder="Masukkan Nomer HP" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" id="password"
                                    placeholder="Masukkan Password">
                                <small><a href="#" class="toggle-password" data-target="password">Lihat
                                        Password</a></small>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Password</label>
                                <input type="password" class="form-control" name="password_confirmation"
                                    id="password_confirmation" placeholder="Masukkan Konfirmasi Password">
                                <small><a href="#" class="toggle-password" data-target="password-confirmation">Lihat
                                        Password</a></small>
                            </div>

                            <div class="form-group">
                                <label for="category">Role</label>
                                <select class="form-select" name="role_id" id="category">
                                    <option value="" disabled>Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ $user->role->id == $role->id ? 'selected' : '' }}>{{ $role->role_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
                                <button type="submit" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                                <a href="{{ route('users.index') }}" class="btn btn-danger me-1 mb-1">Batal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var targetId = this.getAttribute('data-target');
                var input;
                if (targetId === 'password-confirmation') {
                    input = document.getElementById('password_confirmation');
                } else {
                    input = document.getElementById(targetId);
                }
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.textContent = 'Sembunyikan Password';
                    } else {
                        input.type = 'password';
                        this.textContent = 'Lihat Password';
                    }
                }
            });
        });
    </script>

@endsection
