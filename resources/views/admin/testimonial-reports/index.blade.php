@extends('layouts.admin')

@section('page-title', 'Laporan Testimoni')

@section('content')
    <div class="bg-white rounded-xl p-4 shadow">
        <h2 class="text-lg font-bold mb-3">Laporan Masuk</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="py-2">Tanggal</th>
                    <th>Testimoni</th>
                    <th>Alasan</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $r)
                    <tr class="border-b">
                        <td class="py-2">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="font-semibold">{{ optional($r->testimonial)->name }}</div>
                            <div class="text-xs text-gray-500 line-clamp-2">
                                {{ optional($r->testimonial)->message }}
                            </div>
                        </td>
                        <td class="font-semibold text-red-600">{{ $r->reason }}</td>
                        <td class="text-gray-600">{{ $r->note }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-500">
                            Belum ada laporan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </div>
@endsection
