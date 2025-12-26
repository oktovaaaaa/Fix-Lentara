@extends('layouts.admin')

@section('page-title','Testimoni')

@section('content')
<form class="flex gap-3 mb-4">
    <select name="rating">
        <option value="">Semua Rating</option>
        @for($i=5;$i>=1;$i--)
            <option value="{{ $i }}">{{ $i }} ⭐</option>
        @endfor
    </select>

    <label>
        <input type="checkbox" name="reported"> Dilaporkan
    </label>

    <button class="btn">Filter</button>
</form>

<table class="w-full border">
<thead>
<tr>
    <th>Nama</th>
    <th>Rating</th>
    <th>Pesan</th>
    <th>Report</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody>
@foreach($testimonials as $t)
<tr>
    <td>{{ $t->name }}</td>
    <td>{{ $t->rating }} ⭐</td>
    <td>{{ Str::limit($t->message,60) }}</td>
    <td>{{ $t->reports_count }}</td>
    <td>
        <form method="POST" action="{{ route('admin.testimonials.destroy',$t) }}">
            @csrf @method('DELETE')
            <button class="text-red-600">Hapus</button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>

{{ $testimonials->links() }}
@endsection
