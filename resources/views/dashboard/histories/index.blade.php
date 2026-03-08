@extends('dashboard.layouts.main')

@section('container')
    {{-- Tangkap session & Muculkan modal sweetalert --}}
    <div class="flash-success" data-flashsuccess="{{ session('success') }}"></div>

    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Rekap Monitoring</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Rekap Monitoring</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Data Rekapitulasi Monitoring Jamur Tiram</h2>
                <p class="section-lead">
                    Berikut data rekapitulasi monitoring jamur tiram yang di update 1 jam sekali.
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <form class="form-inline ml-auto" action="{{ route('dashboard.controls.index') }}">
                                        <!-- Input Pencarian -->
                                        <div class="form-group mx-sm-3 mb-2">
                                            <input type="date" class="form-control" name="filter"
                                                value="{{ request('filter') ?: date('Y-m-d') }}">
                                        </div>
                                        <!-- Tombol Cari -->
                                        <button type="submit" class="btn btn-primary mb-2 mr-2"><i
                                                class="fas fa-search"></i> Filters</button>
                                        <!-- Tombol Cetak -->
                                        @if ($controls->count() > 0)
                                            <a class="btn btn-success mb-2 mr-2" target="_blank"
                                                href="/dashboard/cetak{{ request()->has('filter') ? '?filter=' . request('filter') : '' }}"><i
                                                    class="fas fa-print"></i> Cetak</a>

                                            <a class="btn btn-warning mb-2"
                                                href="{{ route('dashboard.export.csv', request()->only('filter')) }}">
                                                <i class="fas fa-file-csv"></i> Export CSV
                                            </a>
                                        @endif
                                    </form>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped text-center" id="table-1">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Pukul</th>
                                                <th>Suhu</th>
                                                <th>Kelembapan</th>
                                                <th>Kipas</th>
                                                <th>Humidifier</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($controls as $control)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $control->created_at->format('d-m-Y') }}</td>
                                                    <td>{{ $control->created_at->format('H:i') }}</td>
                                                    <td>{{ $control->suhu }}</td>
                                                    <td>{{ $control->kelembapan }}</td>
                                                    <td>
                                                        <div
                                                            class="badge {{ $control->kipas == 'Hidup' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $control->kipas }}</div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="badge {{ $control->humidifier == 'Hidup' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $control->humidifier }}</div>
                                                    </td>
                                                    <td>
                                                        <form
                                                            action="{{ route('dashboard.controls.destroy', $control->id) }}"
                                                            class="delete-form" method="POST">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="btn btn-danger btn-action"
                                                                title="Delete"><i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('page-script')
    {{-- Penggunaan funtion tombol delete --}}
    <script>
        const flashSuccess = $(".flash-success").data("flashsuccess");

        // Jika terjadi perubahan CRUD
        if (flashSuccess) {
            swal({
                title: "Data",
                text: "Berhasil " + flashSuccess,
                icon: "success",
            });
        }

        // konfirmasi hapus data
        $(document).ready(function() {
            $(".delete-form").on("submit", function(e) {
                e.preventDefault();

                var form = this;

                swal({
                    title: "Apakah kamu yakin?",
                    text: "Data akan dihapus",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
