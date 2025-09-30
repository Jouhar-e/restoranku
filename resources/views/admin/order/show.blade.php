@extends('admin.layouts.master')

@section('title', 'Detail Pesanan')

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
                    <h4 class="card-title">
                        Kode Pesanan : {{ $order->order_code }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p>Dibuat : {{ $order->created_at }}</p>
                            <p>Nama Pelanggan : {{ $order->user->fullname }}</p>
                            <p>Status :
                                <span
                                    class="badge 
                                            @if ($order->status == 'settlement') bg-success 
                                            @elseif($order->status == 'cooked') bg-primary 
                                            @else bg-warning @endif">
                                    {{ $order->status }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>Nomer Meja : {{ $order->table_number }}</p>
                            <p>Metode Pembayaran : {{ $order->payment_method }}</p>
                            <p>Catatan : {{ $order->notes ?? '-' }} </p>
                        </div>
                    </div>
                </div>
            </div>

        </section>

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
                                <th>Gambar</th>
                                <th>Nama Menu</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderItems as $menu)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <img src="{{ asset('img_item_upload/' . $menu->item->img) }}" width="60"
                                            class="img-fluid rounded-top" alt=""
                                            onerror="this.onerror=null;this.src='https://dummyimage.com/300x450/ced4da/6c757d.jpg&text=No+Image';">
                                    </td>
                                    <td>{{ $menu->item->name }}</td>
                                    <td>{{ $menu->quantity }}</td>
                                    <td>{{ 'Rp. ' . number_format($menu->item->price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tr>
                            <th colspan="3">Sub Total</th>
                            <th>:</th>
                            <th>{{ 'Rp. ' . number_format($menu->order->subtotal, 0, ',', '.') }}</th>
                        </tr>
                        <tr>
                            <th colspan="3">Pajak :</th>
                            <th>:</th>
                            <th>{{ 'Rp. ' . number_format($menu->order->tax, 0, ',', '.') }}</th>
                        </tr>
                        <tr>
                            <th colspan="3">Total :</th>
                            <th >:</th>
                            <th>{{ 'Rp. ' . number_format($menu->order->grand_total, 0, ',', '.') }}</th>
                        </tr>
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
