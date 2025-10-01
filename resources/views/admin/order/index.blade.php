@extends('admin.layouts.master')

@section('title', 'Category')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/compiled/css/table-datatable.css') }}">
@endsection

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Pesanan</h3>
                    <p class="text-subtitle text-muted">Data pemesanan.</p>
                </div>
                {{-- <div class="col-12 col-md-6 order-md-2 order-first">
                    <a href="{{ route('orders.create') }}" class="btn btn-primary float-start float-lg-end"><i
                            class="bi bi-plus"></i> Tambah Data</a>
                </div> --}}
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        Simple Datatable
                    </h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">

                            <p><i class="bi bi-check-circle"></i> {{ session('success') }}</p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Pesanan</th>
                                <th>Nama Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>No. Meja</th>
                                <th>Metode Pembayaran</th>
                                <th>Catatan</th>
                                <th>Dibuat Pada</th>
                                <th colspan="2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $order->order_code }}</td>
                                    <td>{{ $order->user->fullname }}</td>
                                    <td>{{ 'Rp. ' . number_format($order->grand_total, 0, ',', '.') }}</td>
                                    <td>
                                        <span
                                            class="badge 
                                            @if ($order->status == 'settlement') bg-success 
                                            @elseif($order->status == 'cooked') bg-primary 
                                            @else bg-warning @endif">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td>{{ $order->table_number }}</td>
                                    <td>{{ $order->payment_method }}</td>
                                    <td>{{ $order->notes ?? '-' }}</td>
                                    <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                                    <td class="">
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm"><i
                                                class="bi bi-eye"></i> Lihat</a>
                                    </td>
                                    <td>
                                        @if (Auth::user()->role->role_name == 'admin' || Auth::user()->role->role_name == 'cashier')
                                            @if ($order->status == 'pending' && $order->payment_method == 'tunai')
                                                <form action="{{ route('orders.statusUpdate', $order->id) }}"
                                                    method="post">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm"><i
                                                            class="bi bi-check"></i> Terima Pesanan</button>
                                                </form>
                                            @endif
                                        @elseif ($order->status == 'settlement' && Auth::user()->role->role_name == 'chef')
                                            <form action="{{ route('orders.statusUpdate', $order->id) }}" method="post">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm"><i
                                                        class="bi bi-check"></i> Pesanan Siap</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/admin/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/admin/static/js/pages/simple-datatables.js') }}"></script>
@endsection
