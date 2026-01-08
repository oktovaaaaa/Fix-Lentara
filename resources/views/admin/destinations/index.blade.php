{{-- resources/views/admin/destinations/index.blade.php (REPLACE FULL) --}}
@extends('layouts.admin')

@section('title', 'Admin - Destinasi')

@section('content')
<div class="a-wrap" data-page="admin-destinations-index">

    {{-- PAGE HEAD --}}
    <div class="a-head">
        <div class="a-head-left">
            <div class="a-head-title">Destinasi (CRUD per Pulau &amp; Suku)</div>
            <div class="a-head-desc">
                Kelola destinasi berdasarkan <strong>Pulau</strong> dan <strong>Suku</strong>.
                Pilih filter untuk melihat data, lalu tambah/edit/hapus destinasi.
            </div>
        </div>

        <div class="a-head-right">
            @if(session('status'))
                <div class="a-alert a-alert-success">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </div>

    {{-- FILTER + INFO --}}
    <div class="a-grid">
        <div class="a-col-6">
            <div class="a-card" data-card data-card-key="admin_destinations_filter">
                <div class="a-card-inner">
                    <div class="a-card-head">
                        <div>
                            <div class="a-card-title">Filter Pulau + Suku</div>
                            <div class="a-card-desc">Pilih pulau lalu pilih suku. Halaman akan auto-load.</div>
                        </div>
                        <div class="a-card-actions">
                            <span class="a-badge"><i class="bx bx-filter-alt"></i> Filter</span>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.destinations.index') }}" class="a-form a-form-tight">
                        <div class="a-grid">
                            <div class="a-col-12">
                                <label class="a-label" for="destIsland">Pilih Pulau</label>
                                <select id="destIsland"
                                        name="island"
                                        class="a-select"
                                        onchange="this.form.submit()">
                                    @foreach($islands as $isl)
                                        <option value="{{ $isl->slug }}" {{ optional($selectedIsland)->slug === $isl->slug ? 'selected' : '' }}>
                                            {{ $isl->name }} ({{ $isl->slug }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="a-col-12">
                                <label class="a-label" for="destTribe">Pilih Suku</label>
                                <select id="destTribe"
                                        name="tribe"
                                        class="a-select"
                                        onchange="this.form.submit()">
                                    @forelse($tribes as $t)
                                        <option value="{{ $t }}" {{ $selectedTribe === $t ? 'selected' : '' }}>
                                            {{ $t }}
                                        </option>
                                    @empty
                                        <option value="">(Belum ada suku untuk pulau ini)</option>
                                    @endforelse
                                </select>
                                <div class="a-help">
                                    Jika suku kosong, berarti di config <strong>tribes.php</strong> belum ada untuk pulau tersebut.
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="a-col-6">
            <div class="a-card" data-card data-card-key="admin_destinations_summary">
                <div class="a-card-inner">
                    <div class="a-card-head">
                        <div>
                            <div class="a-card-title">Ringkasan Pilihan</div>
                            <div class="a-card-desc">Konteks data yang sedang kamu kelola.</div>
                        </div>
                        <div class="a-card-actions">
                            <span class="a-badge"><i class="bx bx-map"></i> Context</span>
                        </div>
                    </div>

                    <div class="a-card-body">
                        <div class="a-kv">
                            <div class="k">Pulau</div>
                            <div class="v">{{ optional($selectedIsland)->name ?? '-' }}</div>
                        </div>
                        <div class="a-kv">
                            <div class="k">Suku</div>
                            <div class="v">{{ $selectedTribe ?: '-' }}</div>
                        </div>
                        <div class="a-kv">
                            <div class="k">Total Destinasi</div>
                            <div class="v">{{ $rows->count() }}</div>
                        </div>
                    </div>

                    <div class="a-actions">
                        @if($selectedIsland && $selectedTribe)
                            <a href="{{ route('admin.destinations.create', ['island' => $selectedIsland->slug, 'tribe' => $selectedTribe]) }}"
                               class="a-btn a-btn-primary">
                                <i class="bx bx-plus"></i> Tambah Destinasi
                            </a>
                        @else
                            <div class="a-note">
                                Pilih pulau &amp; suku dulu untuk menambah destinasi.
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- TABLE LIST --}}
    <div class="a-card" data-card data-card-key="admin_destinations_table">
        <div class="a-card-inner">
            <div class="a-card-head">
                <div>
                    <div class="a-card-title">Daftar Destinasi</div>
                    <div class="a-card-desc">
                        Edit untuk mengubah data. Hapus untuk menghapus destinasi.
                    </div>
                </div>
                <div class="a-card-actions">
                    <span class="a-badge"><i class="bx bx-table"></i> Table</span>
                </div>
            </div>

            <div class="a-tableWrap">
                <table class="a-table">
                    <thead>
                        <tr>
                            <th class="col-num">#</th>
                            <th>Nama</th>
                            <th>Lokasi</th>
                            <th class="col-rating">Rating</th>
                            <th class="col-img">Gambar</th>
                            <th class="col-active">Aktif</th>
                            <th class="col-actions">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($rows as $i => $row)
                            <tr>
                                <td class="col-num">{{ $i + 1 }}</td>

                                <td>
                                    <div class="a-tdMain">{{ $row->name }}</div>
                                    <div class="a-tdSub">
                                        ID: {{ $row->id ?? '—' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="a-tdMain">{{ $row->location ?? '—' }}</div>
                                </td>

                                <td class="col-rating">
                                    <span class="a-pillBadge">
                                        <i class="bx bxs-star"></i>
                                        {{ number_format((float)$row->rating, 1) }}
                                    </span>
                                </td>

                                <td class="col-img">
                                    @if($row->image_display_url)
                                        <a href="{{ $row->image_display_url }}" target="_blank" class="a-link">
                                            Lihat
                                        </a>
                                    @else
                                        <span class="a-muted">—</span>
                                    @endif
                                </td>

                                <td class="col-active">
                                    @if($row->is_active)
                                        <span class="a-status a-status-on">Aktif</span>
                                    @else
                                        <span class="a-status a-status-off">Off</span>
                                    @endif
                                </td>

                                <td class="col-actions">
                                    <div class="a-rowActions">
                                        <a href="{{ route('admin.destinations.edit', $row) }}"
                                           class="a-btn a-btn-sm">
                                            <i class="bx bx-edit"></i> Edit
                                        </a>

                                        <form action="{{ route('admin.destinations.destroy', $row) }}" method="POST" class="a-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Hapus destinasi ini?')"
                                                    class="a-btn a-btn-sm a-btn-danger">
                                                <i class="bx bx-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="a-empty">
                                        Belum ada destinasi untuk pulau &amp; suku yang dipilih.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection
