<?php
    
    public function save_scan($id = "", $nim = "")
    {
        // Load the QR code library
        $hasilpost = explode('|', $this->input->post('qr_data'));
        $nim = $hasilpost[0];
        //$id = "050127120720170304";
        $id = $hasilpost[1];

        // Validasi
        if (empty($nim)) {
            echo json_encode([
                'success' => false,
                'message' => 'Data QR tidak ditemukan' . $data['code']
            ]);
            return;
        } else {
            $cekp = $this->db->query("SELECT * FROM pesexxx where kd_sexxxx='$id' and nim='$nim'");
            if ($cekp->num_rows() == 1) {
                $tpdata = $cekp->row();
                if ($tpdata->pembayaran == 1) {
                    if ($tpdata->absensi != 1) {
                        // $data = array(
                        //     'absensi'   => 1,
                        //     'jam_absen' => date('Y-m-d H:i:s')
                        // );
                        // $this->db->where('nim', $nim);
                        // $this->db->where('kd_sertifikat', $id);
                        // $add = $this->db->update('peserta', $data);
                        $date = date('Y-m-d H:i:s');
                        $add = $this->db->query("UPDATE pesexxx SET absxx='1', jam_xxx='$date'  where kd_sertixxxx='$id' and nim='$nim'");
                        if ($add) {
                            echo json_encode([
                                'success' => true,
                                'status' => "success",
                                'message' => "<strong><i class='glyphicon glyphicon-ok-circle'></i> Sukses ✔ </strong> $nim  Berhasil disimpan."
                            ]);
                            // redirect('kegiatan/peserta_kegiatan_detail/' . $key . '/' . $tpdata->nama_mahasiswa . '/berhasil');
                        } else {
                            echo json_encode([
                                'success' => false,
                                'status' => "danger",
                                'message' =>  "<strong><i class='glyphicon glyphicon-ok-alert'></i> Gagal !</strong> $nim Gagal Simpan Presensi."
                            ]);
                            // redirect('kegiatan/peserta_kegiatan_detail/' . $key . '/' . $tpdata->nama_mahasiswa . '/gagal_presensi');
                        }
                    } else {
                        echo json_encode([
                            'success' => false,
                            'scan_id' => $nim,
                            'status' => "info",
                            'message' => "<strong><i class='glyphicon glyphicon-ok-circle'></i> Sukses ✔ </strong>  $nim Sudah Presensi"
                        ]);

                        // redirect('kegiatan/peserta_kegiatan_detail/' . $key . '/' . $tpdata->nama_mahasiswa . '/sudah_presensi');
                    }
                } else {
                    // redirect('kegiatan/peserta_kegiatan_detail/' . $key . '/' . $tpdata->nama_mahasiswa . '/belum_bayar');
                    echo json_encode([
                        'success' => false,
                        'scan_id' => $nim,
                        'status' => "warning",
                        'message' => " <strong><i class='glyphicon glyphicon-ok-alert'></i> Gagal !</strong> $nim Belum Melakukan Pembayaran, Silahkan Aktifkan Pembayaran terlebih dahulu."

                    ]);
                }
            } else {
                // redirect('kegiatan/peserta_kegiatan_detail/' . $key . '/' . $nim . '/data_kosong');
                echo json_encode([
                    'success' => false,
                    'status' => "danger",
                    'message' => "<strong><i class='glyphicon glyphicon-ok-alert'></i> Gagal !</strong> $nim Tidak Terdaftar, Silahkan Daftar Terlebih Dahulu."
                ]);
            }
        }

      
    }

    ?>
