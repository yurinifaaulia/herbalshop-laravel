<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    public function index()
    {
        $menunggu = DB::table('v_pengadaan')->where('status', 'Pending')->orderBy('idpengadaan')->get();
        foreach ($menunggu as $p) {
            $p->items   = $this->rincian($p->idpengadaan);
            $p->lengkap = $p->items->isNotEmpty() && $p->items->every(fn ($i) => $i->sisa == 0);
        }

        $riwayat = DB::table('v_penerimaan')->orderByDesc('idpenerimaan')->get();
        $detail = DB::table('detail_penerimaan as dp')
            ->join('barang as b', 'b.idbarang', '=', 'dp.barang_idbarang')
            ->select('dp.idpenerimaan', 'b.nama', 'dp.jumlah_terima')
            ->get()
            ->groupBy('idpenerimaan');

        $users = DB::table('v_user')->get();

        return view('penerimaan', compact('menunggu', 'riwayat', 'detail', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idpengadaan' => 'required|integer|exists:pengadaan,idpengadaan',
            'iduser'      => 'required|integer|exists:user,iduser',
            'terima'      => 'required|array',
            'terima.*'    => 'nullable|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $status = DB::table('pengadaan')->where('idpengadaan', $data['idpengadaan'])->value('status');
                if ($status === 'T') {
                    throw new \RuntimeException('Pengadaan ini sudah tuntas.');
                }

                $baris = [];
                foreach ($this->rincian($data['idpengadaan']) as $it) {
                    $qty = (int) ($data['terima'][$it->idbarang] ?? 0);
                    if ($qty > $it->sisa) {
                        throw new \RuntimeException("Jumlah terima {$it->nama} melebihi sisa ({$it->sisa}).");
                    }
                    if ($qty > 0) {
                        $baris[] = [
                            'barang_idbarang'     => $it->idbarang,
                            'jumlah_terima'       => $qty,
                            'harga_satuan_terima' => $it->harga,
                            'sub_total_terima'    => $qty * $it->harga,
                        ];
                    }
                }
                if (!$baris) {
                    throw new \RuntimeException('Isi jumlah terima minimal untuk satu barang.');
                }

                $idpenerimaan = DB::table('penerimaan')->insertGetId([
                    'created_at'  => now(),
                    'status'      => 'P',
                    'idpengadaan' => $data['idpengadaan'],
                    'iduser'      => $data['iduser'],
                ]);

                DB::table('detail_penerimaan')->insert(array_map(
                    fn ($b) => $b + ['idpenerimaan' => $idpenerimaan],
                    $baris
                ));
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['terima' => $e->getMessage()]);
        }

        return redirect('/penerimaan')->with('ok', 'Penerimaan barang disimpan.');
    }

    // penerimaan tuntas -> pengadaan ikut tuntas
    public function tuntas(int $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $status = DB::table('pengadaan')->where('idpengadaan', $id)->value('status');
                if ($status === null) {
                    throw new \RuntimeException('Pengadaan tidak ditemukan.');
                }
                if ($status === 'T') {
                    throw new \RuntimeException('Pengadaan ini sudah tuntas.');
                }

                $rincian = $this->rincian($id);
                if ($rincian->isEmpty() || $rincian->contains(fn ($i) => $i->sisa > 0)) {
                    throw new \RuntimeException('Belum semua barang diterima, penerimaan belum bisa dituntaskan.');
                }

                DB::table('penerimaan')->where('idpengadaan', $id)->update(['status' => 'T']);
                DB::table('pengadaan')->where('idpengadaan', $id)->update(['status' => 'T']);
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['tuntas' => $e->getMessage()]);
        }

        return redirect('/penerimaan')->with('ok', 'Penerimaan dan pengadaan berstatus Tuntas.');
    }

    // per barang: dipesan, sudah diterima, sisa
    private function rincian(int $idpengadaan)
    {
        $dipesan = DB::table('detail_pengadaan as d')
            ->join('barang as b', 'b.idbarang', '=', 'd.idbarang')
            ->where('d.idpengadaan', $idpengadaan)
            ->groupBy('d.idbarang', 'b.nama')
            ->select('d.idbarang', 'b.nama',
                     DB::raw('MAX(d.harga_satuan) as harga'),
                     DB::raw('SUM(d.jumlah) as jumlah'))
            ->get();

        $sudah = DB::table('detail_penerimaan as dp')
            ->join('penerimaan as pn', 'pn.idpenerimaan', '=', 'dp.idpenerimaan')
            ->where('pn.idpengadaan', $idpengadaan)
            ->groupBy('dp.barang_idbarang')
            ->select('dp.barang_idbarang', DB::raw('SUM(dp.jumlah_terima) as total'))
            ->pluck('total', 'barang_idbarang');

        return $dipesan->map(function ($d) use ($sudah) {
            $d->jumlah   = (int) $d->jumlah;
            $d->harga    = (int) $d->harga;
            $d->diterima = (int) ($sudah[$d->idbarang] ?? 0);
            $d->sisa     = max(0, $d->jumlah - $d->diterima);

            return $d;
        });
    }
}
