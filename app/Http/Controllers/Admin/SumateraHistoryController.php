<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SumateraHistory;
use Illuminate\Http\Request;

class SumateraHistoryController extends Controller
{
    public function index()
    {
        $histories = SumateraHistory::orderBy('tribe')
            ->orderBy('sort_order')
            ->orderBy('period')
            ->get();

        return view('admin.sumatera_histories.index', compact('histories'));
    }

    public function create()
    {
        $history = new SumateraHistory();

        return view('admin.sumatera_histories.form', [
            'history' => $history,
            'mode'    => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        SumateraHistory::create($data);

        return redirect()
            ->route('admin.sumatera-histories.index')
            ->with('success', 'Sejarah berhasil ditambahkan.');
    }

    public function edit(SumateraHistory $sumatera_history)
    {
        return view('admin.sumatera_histories.form', [
            'history' => $sumatera_history,
            'mode'    => 'edit',
        ]);
    }

    public function update(Request $request, SumateraHistory $sumatera_history)
    {
        $data = $this->validateData($request);

        $sumatera_history->update($data);

        return redirect()
            ->route('admin.sumatera-histories.index')
            ->with('success', 'Sejarah berhasil diperbarui.');
    }

    public function destroy(SumateraHistory $sumatera_history)
    {
        $sumatera_history->delete();

        return redirect()
            ->route('admin.sumatera-histories.index')
            ->with('success', 'Sejarah berhasil dihapus.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'tribe'      => ['required', 'in:aceh,batak,minang'],
            'period'     => ['required', 'string', 'max:255'],
            'title'      => ['required', 'string', 'max:255'],
            'body'       => ['required', 'string'],
            'more_link'  => ['nullable', 'url'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
