<?php

namespace App\Modules\Dashboard\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $table                = 'penjualan';

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

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

    // hitung total data pada kontak
    public function getCountKontak()
    {
        return $this->db->table("kontak")->countAll();
    }

    // hitung total data pada user
    public function getCountUser()
    {
        return $this->db->table("login")->countAll();
    }

    public function getBackups()
    {
        $query = $this->db->table('backups')
            ->where('DATE(created_at) =', date('Y-m-d'));
        return $query->get()->getResultArray();
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

    public function sumQtyHariini()
    {
        $query = $this->db->table('penjualan_item')
            ->select('sum(qty) as total')
            ->where('DATE(created_at) =', date('Y-m-d'));
        return $query->get()->getRow()->total;
    }

    public function sumLabaHariini()
    {
        $this->select('sum(total_laba) as total');
        $this->where('DATE(created_at) =', date('Y-m-d'));
        return $this->get()->getRow()->total;
    }

    public function sumLabaHarikemarin()
    {
        $this->select('sum(total_laba) as total');
        $this->where('DATE(created_at) =', date('Y-m-d', strtotime('-1 days')));
        return $this->get()->getRow()->total;
    }

    public function countTrxHariini()
    {
        $this->where('DATE(created_at) =', date('Y-m-d'));
        return count($this->get()->getResultArray());
    }

    public function countTrxHarikemarin()
    {
        $this->where('DATE(created_at) =', date('Y-m-d', strtotime('-1 days')));
        return count($this->get()->getResultArray());
    }

    public function totalTrxHariini()
    {
        $this->select('(sum(subtotal)+sum(pembulatan)-sum(diskon)) as total');
        $this->where('DATE(created_at) =', date('Y-m-d'));
        return $this->get()->getRow()->total;
    }

    public function totalTrxHarikemarin()
    {
        $this->select('(sum(subtotal)+sum(pembulatan)-sum(diskon)) as total');
        $this->where('DATE(created_at) =', date('Y-m-d', strtotime('-1 days')));
        return $this->get()->getRow()->total;
    }

    public function kasMasukHariini()
    {
        $query = $this->db->table('cashflow')
            ->select('sum(pemasukan) as total')
            ->like('jenis', 'Pemasukan')
            ->where('DATE(created_at) =', date('Y-m-d'));
        return $query->get()->getRow()->total;
    }

    public function kasMasukHarikemarin()
    {
        $query = $this->db->table('cashflow')
            ->select('sum(pemasukan) as total')
            ->like('jenis', 'Pemasukan')
            ->where('DATE(created_at) =', date('Y-m-d', strtotime('-1 days')));
        return $query->get()->getRow()->total;
    }

    public function kasKeluarHariini()
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

    public function jumlahHutang()
    {
        $query = $this->db->table('hutang')
            ->select('sum(jumlah_hutang) as total');
        return $query->get()->getRow()->total;
    }

    public function sisaHutang()
    {
        $query = $this->db->table('hutang')
            ->select('sum(sisa_hutang) as total');
        return $query->get()->getRow()->total;
    }

    public function jumlahPiutang()
    {
        $query = $this->db->table('piutang')
            ->select('sum(jumlah_piutang) as total');
        return $query->get()->getRow()->total;
    }

    public function sisaPiutang()
    {
        $query = $this->db->table('piutang')
            ->select('sum(sisa_piutang) as total');
        return $query->get()->getRow()->total;
    }

    public function sisaPiutangHariini()
    {
        $query = $this->db->table('piutang')
            ->select('sum(sisa_piutang) as total')
            ->where('DATE(tanggal) =', date('Y-m-d'));
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
}
