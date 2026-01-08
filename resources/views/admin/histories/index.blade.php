{{-- resources/views/admin/histories/index.blade.php (REPLACE FULL) --}}
@extends('layouts.admin')

@section('title', 'History Pulau & Suku')

@section('content')
@php
    // keep original variables behavior
    $islandId = $islandId ?? request('island_id');
    $tribe    = $tribe ?? request('tribe');
@endphp

<div class="a-wrap" data-page="admin-histories-index">

    {{-- PAGE HEAD --}}
    <div class="a-head">
        <div class="a-head-left">
            <div class="a-head-title">History Pulau &amp; Suku</div>
            <div class="a-head-desc">
                Kelola data history berdasarkan Pulau dan Suku. Kamu bisa filter, tambah, edit, dan hapus.
                Tampilan mengikuti UI kit admin agar konsisten.
            </div>
        </div>

        <div class="a-head-right" style="display:flex; gap:10px; align-items:center; justify-content:flex-end; flex-wrap:wrap;">
            <a href="{{ route('admin.histories.create') }}" class="a-btn a-btn-primary">
                <i class="bx bx-plus"></i> Tambah History
            </a>
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="a-alert a-alert-success" style="margin-bottom:12px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- FILTER CARD --}}
    <div class="a-card" data-card data-card-key="admin_histories_filter">
        <div class="a-card-inner">
            <div class="a-card-head">
                <div>
                    <div class="a-card-title">Filter</div>
                    <div class="a-card-desc">Pilih Pulau dan/atau Suku untuk menyaring data.</div>
                </div>
                <div class="a-card-actions">
                    <span class="a-badge"><i class="bx bx-filter-alt"></i> Filter</span>
                </div>
            </div>

            <form method="GET" class="a-form">
                <div class="a-grid">
                    {{-- Pulau --}}
                    <div class="a-col-4">
                        <label class="a-label">Pulau</label>
                        <select name="island_id" class="a-select">
                            <option value="">Semua Pulau</option>
                            @foreach($islands as $island)
                                <option value="{{ $island->id }}" @selected($islandId == $island->id)>
                                    {{ $island->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Suku --}}
                    <div class="a-col-4">
                        <label class="a-label">Suku</label>
                        <select name="tribe" class="a-select">
                            <option value="">Semua Suku</option>
                            @foreach($tribes as $t)
                                <option value="{{ $t }}" @selected($tribe === $t)>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="a-col-4">
                        <div class="a-actions" style="margin-top:26px;">
                            <button type="submit" class="a-btn a-btn-primary">
                                <i class="bx bx-search"></i> Filter
                            </button>

                            @if($islandId || $tribe)
                                <a href="{{ route('admin.histories.index') }}" class="a-btn">
                                    <i class="bx bx-refresh"></i> Reset
                                </a>
                            @endif
                        </div>

                        @if($islandId || $tribe)
                            <div class="a-note">
                                Filter aktif:
                                @if($islandId) <strong>Pulau</strong> dipilih. @endif
                                @if($tribe) <strong>Suku</strong> dipilih. @endif
                            </div>
                        @endif
                    </div>
                </div>
            </form>

        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="a-card" data-card data-card-key="admin_histories_table" style="margin-top:12px;">
        <div class="a-card-inner">
            <div class="a-card-head">
                <div>
                    <div class="a-card-title">Daftar History</div>
                    <div class="a-card-desc">Klik Edit untuk mengubah data. Hapus untuk menghapus data.</div>
                </div>
                <div class="a-card-actions">
                    <span class="a-badge"><i class="bx bx-table"></i> Table</span>
                </div>
            </div>

            <div class="a-tableWrap">
                <table class="a-table">
                    <thead>
                        <tr>
                            <th>Pulau</th>
                            <th>Suku</th>
                            <th>Tahun / Waktu</th>
                            <th>Judul</th>
                            <th style="width:120px;">Header?</th>
                            <th class="col-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                            <tr>
                                <td>
                                    <div class="a-tdMain">{{ $history->island->name ?? '-' }}</div>
                                    <div class="a-tdSub">ID: {{ $history->island_id }}</div>
                                </td>

                                <td>
                                    <div class="a-tdMain">{{ $history->tribe }}</div>
                                </td>

                                <td>
                                    <div class="a-tdMain">{{ $history->year_label }}</div>
                                </td>

                                <td>
                                    <div class="a-tdMain">{{ \Illuminate\Support\Str::limit($history->title, 50) }}</div>
                                </td>

                                {{-- INFO HEADER (TRIBE PAGE) --}}
                                <td>
                                    @php
                                        $hasHeader = \App\Models\TribePage::where('island_id', $history->island_id)
                                            ->where('tribe_key', $history->tribe)
                                            ->exists();
                                    @endphp

                                    @if($hasHeader)
                                        <span class="a-status a-status-on">Ada</span>
                                    @else
                                        <span class="a-status a-status-off">Belum</span>
                                    @endif
                                </td>

                                <td class="col-actions">
                                    <div class="a-rowActions">
                                        <a href="{{ route('admin.histories.edit', $history) }}"
                                           class="a-btn a-btn-sm">
                                            <i class="bx bx-edit"></i> Edit
                                        </a>

                                        <form action="{{ route('admin.histories.destroy', $history) }}"
                                              method="POST"
                                              class="a-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="a-btn a-btn-danger a-btn-sm">
                                                <i class="bx bx-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="a-empty" style="margin-top:0;">
                                        Belum ada data history.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div style="margin-top:12px;">
                {{ $histories->withQueryString()->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
