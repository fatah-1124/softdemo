<?php

namespace App\Modules\Statistik\Models;

/*
PT ITSHOP BISNIS DIGITAL
Website: https://itshop.biz.id
Toko Online: ITSHOP Purwokerto (https://Tokopedia.com/itshoppwt, https://Shopee.co.id/itshoppwt, https://Toco.id/store/itshop-purwokerto)
Dibuat oleh: Hari Wicaksono, S.Kom
04-2022
*/

use CodeIgniter\Model;

class StatistikModel extends Model
{
    protected $table                = 'penjualan';

    // hitung total data pada transaction
    public function getCountTrx()
    {
        return $this->db->table("penjualan")->countAll();
    }

    // hitung total data pada barang
    public function getcountBarang()
    {
        return $this->db->table("barang")->countAll();
    }

    // hitung total data pada Kontak
    public function getCountKontak()
    {
        return $this->db->table("kontak")->countAll();
    }

    // hitung total data pada user
    public function getCountUser()
    {
        return $this->db->table("login")->countAll();
    }

    public function chartTransaksi($date)
    {
        $this->like('created_at', $date, 'after');
        return count($this->get()->getResultArray());
    }

    public function chartHarian($date)
    {
        $this->like('created_at', $date, 'after');
        return count($this->get()->getResultArray());
    }

    public function chartPemasukan($date)
    {
        $this->select('(sum(subtotal)+sum(pembulatan)-sum(diskon)) as total');
        $this->like('created_at', $date, 'after');
        return $this->get()->getRow()->total;
    }

    public function chartSisaPiutang($date)
    {
        $query = $this->db->table('piutang')
            ->select('sum(sisa_piutang) as total')
            ->like('DATE(tanggal)', $date, 'after');
        return $query->get()->getRow()->total;
    }

    public function countTrxHariini($tgl)
    {
        $this->where('DATE(created_at) =', $tgl);
        return count($this->get()->getResultArray());
    }

    public function countTrxHarikemarin($tgl)
    {
        $this->where('DATE(created_at) =', date('Y-m-d', strtotime($tgl . '-1 days')));
        return count($this->get()->getResultArray());
    }

    public function totalTrxHariini($tgl)
    {
        $this->select('(sum(subtotal)+sum(pembulatan)-sum(diskon)) as total');
        $this->where('DATE(created_at) =', $tgl);
        return $this->get()->getRow()->total;
    }

    public function totalTrxHarikemarin($tgl)
    {
        $this->select('(sum(subtotal)+sum(pembulatan)-sum(diskon)) as total');
        $this->where('DATE(created_at) =', date('Y-m-d', strtotime($tgl . '-1 days')));
        return $this->get()->getRow()->total;
    }

    public function sisaHutang()
    {
        $query = $this->db->table('hutang')
            ->select('sum(sisa_hutang) as total');
        return $query->get()->getRow()->total;
    }

    public function sisaPiutang()
    {
        $query = $this->db->table('piutang')
            ->select('sum(sisa_piutang) as total');
        return $query->get()->getRow()->total;
    }

    public function sisaPiutangHariini($tgl)
    {
        $query = $this->db->table('piutang')
            ->select('sum(sisa_piutang) as total')
            ->where('DATE(tanggal) =', $tgl);
        return $query->get()->getRow()->total;
    }

    public function sisaPiutangHarikemarin()
    {
        $query = $this->db->table('piutang')
            ->select('sum(sisa_piutang) as total')
            ->where('DATE(tanggal) =', date('Y-m-d', strtotime('-1 days')));
        return $query->get()->getRow()->total;
    }

    public function hutangAkanTempo()
    {
        $query = $this->db->table('hutang')
            ->where('DATE(jatuh_tempo) >', date('Y-m-d'))
            ->where('status_hutang', 0);
        return $query->countAllResults();
    }

    public function hutangTempoHariini()
    {
        $query = $this->db->table('hutang')
            ->where('DATE(jatuh_tempo) =', date('Y-m-d'))
            ->where('status_hutang', 0);
        return $query->countAllResults();
    }

    public function hutangLewatTempo()
    {
        $query = $this->db->table('hutang')
            ->where('DATE(jatuh_tempo) <', date('Y-m-d'))
            ->where('status_hutang', 0);
        return $query->countAllResults();
    }

    public function piutangAkanTempo()
    {
        $query = $this->db->table('piutang')
            ->where('DATE(jatuh_tempo) >', date('Y-m-d'))
            ->where('status_piutang', '0');
        return $query->countAllResults();
    }

    public function piutangTempoHariini()
    {
        $query = $this->db->table('piutang')
            ->where('DATE(jatuh_tempo) =', date('Y-m-d'))
            ->where('status_piutang', '0');
        return $query->countAllResults();
    }

    public function piutangLewatTempo()
    {
        $query = $this->db->table('piutang')
            ->where('DATE(jatuh_tempo) <', date('Y-m-d'))
            ->where('status_piutang', '0');
        return $query->countAllResults();
    }

    public function sumQtyHariini($tgl)
    {
        $query = $this->db->table('penjualan_item')
            ->select('sum(qty) as total')
            ->join("penjualan", "penjualan.id_penjualan = penjualan_item.id_penjualan")
            ->where('DATE(penjualan.created_at) =', $tgl);
        return $query->get()->getRow()->total;
    }

    public function sumLabaHariini($tgl)
    {
        $this->select('sum(total_laba) as total');
        $this->where('DATE(created_at) =', $tgl);
        return $this->get()->getRow()->total;
    }

    public function sumLabaHarikemarin($tgl)
    {
        $this->select('sum(total_laba) as total');
        $this->where('DATE(created_at) =', date('Y-m-d', strtotime($tgl . '-1 days')));
        return $this->get()->getRow()->total;
    }

    public function kasKeluarHariini($tgl)
    {
        $query = $this->db->table('cashflow')
            ->select('sum(pengeluaran) as total')
            ->groupStart()
            ->like('jenis', 'Pengeluaran')
            ->orLike('jenis', 'Mutasi ke Bank', 'before')
            ->groupEnd()
            ->where('DATE(created_at) =', date('Y-m-d'));
        return $query->get()->getRow()->total;
    }

    public function kasKeluarHarikemarin($tgl)
    {
        $query = $this->db->table('cashflow')
            ->select('sum(pengeluaran) as total')
            ->groupStart()
            ->like('jenis', 'Pengeluaran')
            ->orLike('jenis', 'Mutasi ke Bank', 'before')
            ->groupEnd()
            ->where('DATE(tanggal) =', date('Y-m-d', strtotime($tgl . '-1 days')));
        return $query->get()->getRow()->total;
    }

    public function bankKeluarHariini($tgl)
    {
        $query = $this->db->table('bank')
            ->select('sum(pengeluaran) as total')
            ->groupStart()
            ->like('jenis', 'Pengeluaran')
            ->orLike('jenis', 'Mutasi ke Kas', 'before')
            ->groupEnd()
            ->where('DATE(created_at) =', $tgl);
        return $query->get()->getRow()->total;
    }

    public function bankKeluarHarikemarin($tgl)
    {
        $query = $this->db->table('bank')
            ->select('sum(pengeluaran) as total')
            ->groupStart()
            ->like('jenis', 'Pengeluaran')
            ->orLike('jenis', 'Mutasi ke Kas', 'before')
            ->groupEnd()
            ->where('DATE(tanggal) =', date('Y-m-d', strtotime($tgl . '-1 days')));
        return $query->get()->getRow()->total;
    }

    public function barangTerlaris()
    {
        $db      = \Config\Database::connect();
        $db->simpleQuery("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $builder = $db->table('penjualan_item n');
        $builder->select("b.nama_barang, b.satuan_barang, b.satuan_nilai, sum(n.qty) qty");
        $builder->join("barang b", "b.id_barang = n.id_barang");
        $builder->groupBy("n.id_barang");
        $builder->orderBy("n.qty", "DESC");
        $builder->limit("5");
        $query = $builder->get()->getResultArray();
        return $query;
        
    }

}
