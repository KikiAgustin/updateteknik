<?php
class ModelAffiliateInformation extends Model {
	public function getProfile() {

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate` WHERE affiliate_id = '" . (int)$this->affiliate->getId() . "'");
		return $query->row;
	}

	public function getKomisi(){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_komisi` ");
		return $query->rows;
	}

	public function getKomisiByProdukId($produk_id){
		$query = $this->db->query("SELECT * FROM oc_affiliate_komisi WHERE produk_id='$produk_id' ");
		$data = $query->row;
		if($data){
			return (int) $data['komisi'];
		}

	}

	public function addKlik($produk_id, $tracking){
		$query = $this->db->query("SELECT affiliate_id FROM oc_affiliate WHERE code='$tracking'");
		$data = $query->row;
	
		if($data){
			$affiliate_id = (int) $data['affiliate_id'];
			
			$time_limit = 10;
	
			$checkQuery = $this->db->query("
				SELECT COUNT(*) as total 
				FROM oc_affiliate_klik 
				WHERE produk_id='$produk_id' 
				  AND affiliate_id='$affiliate_id' 
				  AND TIMESTAMPDIFF(SECOND, tanggal, NOW()) < $time_limit
			");
			$checkData = $checkQuery->row;
	
			if ($checkData['total'] == 0) {
				$this->db->query("
					INSERT INTO oc_affiliate_klik (produk_id, affiliate_id, tanggal) 
					VALUES ('$produk_id', '$affiliate_id', NOW())
				");
			}
		}	
	}
	

	public function getIdAffiliate($tracking){
		$query = $this->db->query("SELECT affiliate_id FROM oc_affiliate WHERE code='$tracking' ");
		$data = $query->row;
		return (int) $data['affiliate_id'];
	}

	public function getTotalKlik($produk_id, $affiliate_id){
		$query = $this->db->query("SELECT COUNT(*) FROM oc_affiliate_klik WHERE produk_id='$produk_id' AND affiliate_id='$affiliate_id' ");
		return $query->row;
	}


	public function getTransactionSuccess($tracking){
		$query = $this->db->query("SELECT order_id, date_added, status_transaksi FROM oc_order WHERE tracking='$tracking' AND status_transaksi='5' ");
		return $query->rows;
	}

	public function getAllTransaction($tracking){
		$query = $this->db->query("SELECT order_id, date_added, status_transaksi FROM oc_order WHERE tracking='$tracking' ORDER BY order_id DESC");
		return $query->rows;
	}

	public function getTotalProductTransaction($order_id){
		$query = $this->db->query("SELECT quantity, product_id, name, quantity, price, paid_status, tarif_komisi FROM oc_order_product WHERE order_id='$order_id' ");
		return $query->rows;
	}

	public function getTarifKomisi($id_affiliate_member){
		$query = $this->db->query("SELECT member,tarif_komisi FROM oc_affiliate_member WHERE id_affiliate_member='$id_affiliate_member' ");
		return $query->row;
	}

	public function getTarifKomisiByMember(){
		$this->load->model('affiliate/information');
		$code = $this->affiliate->getCode();
		$transactions = $this->model_affiliate_information->getTransactionSuccess($code);

        $result = array();
        foreach ($transactions as $transaction) {
            $order_id = $transaction['order_id'];
            
            $totalorder = $this->model_affiliate_information->getTotalProductTransaction($order_id);
            $result[$order_id] = $totalorder;
        
        }

        $transaction_product = array();

        foreach ($result as $order) {
            foreach ($order as $item) {
                $product_id = $item['product_id'];
                $quantity = (int) $item['quantity'];

                if (!isset($transaction_product[$product_id])) {
                    $transaction_product[$product_id] = array(
                        'total_quantity' => 0,
                        'transaksi' => 0
                    );
                }

                $transaction_product[$product_id]['total_quantity'] += $quantity;
                $transaction_product[$product_id]['transaksi'] += 1;
            }
        }


        $totaltransaksi = count($result);
        $totalQuantity = 
        (isset($transaction_product[334]['total_quantity']) ? $transaction_product[334]['total_quantity'] : 0) +
        (isset($transaction_product[614]['total_quantity']) ? $transaction_product[614]['total_quantity'] : 0) +
        (isset($transaction_product[883]['total_quantity']) ? $transaction_product[883]['total_quantity'] : 0);

        if ($totaltransaksi < 100 || $totalQuantity < 100) {
            $statusmember = 1;
        } elseif (($totaltransaksi >= 100 && $totaltransaksi < 500) || ($totalQuantity >= 100 && $totalQuantity < 500)) {
            $statusmember = 2;
        } elseif ($totaltransaksi >= 500 || $totalQuantity >= 500) {
            $statusmember = 3;
        }

		$tarif_komisi = $this->model_affiliate_information->getTarifKomisi($statusmember);

		return $tarif_komisi;
	}

	public function getAllTransactionByAffiliate(){

		$this->load->model('affiliate/information');
		$code = $this->affiliate->getCode();
		$transactions = $this->model_affiliate_information->getAllTransaction($code);

		$result = array();
        foreach ($transactions as $transaction) {
            $order_id = $transaction['order_id'];
            
            $totalorder = $this->model_affiliate_information->getTotalProductTransaction($order_id);
            $result[$order_id] = $totalorder;
        
        }

        $transaction_product = array();

        foreach ($result as $order) {
            foreach ($order as $item) {
                $product_id = $item['product_id'];
                $quantity = (int) $item['quantity'];

                if (!isset($transaction_product[$product_id])) {
                    $transaction_product[$product_id] = array(
                        'total_quantity' => 0,
                        'transaksi' => 0
                    );
                }

                $transaction_product[$product_id]['total_quantity'] += $quantity;
                $transaction_product[$product_id]['transaksi'] += 1;
            }
        }

		return $transaction_product;

	}

	public function getDetailAllTransactions() {
		$this->load->model('affiliate/information');
		$this->load->model('catalog/product');
		$code = $this->affiliate->getCode();
		$transactions = $this->model_affiliate_information->getAllTransaction($code);
	
		$result = array();
		$base_url = "https://gudangmaterials.id/image/cache/";
		$suffix = "-500x500.png";
	
		$allowed_product_ids = array(614, 883, 334);
	
		foreach ($transactions as $transaction) {
			$order_id = $transaction['order_id'];
			$date_added = $transaction['date_added'];
			if($transaction['status_transaksi'] == 5){
				$status_transaksi = "Berhasil";
				$class_transaksi = "badge-success";
			} else if($transaction['status_transaksi'] == 14){
				$status_transaksi = "Gagal";
				$class_transaksi = "badge-danger";
			} else if($transaction['status_transaksi'] == 7){
				$status_transaksi = "Gagal";
				$class_transaksi = "badge-danger";
			} else if($transaction['status_transaksi'] == 10){
				$status_transaksi = "Gagal";
				$class_transaksi = "badge-danger";
			} else {
				$status_transaksi = "DiProses";
				$class_transaksi = "badge-secondary";	
			}
	
			// Format tanggal menjadi "10 November 2024"
			$formatted_date = date('d F Y', strtotime($date_added));
	
			$totalorder = $this->model_affiliate_information->getTotalProductTransaction($order_id);
	
			$filtered_products = array_filter($totalorder, function ($product) use ($allowed_product_ids) {
				return in_array($product['product_id'], $allowed_product_ids);
			});
	
			foreach ($filtered_products as &$product) {
				$gambar = $this->model_catalog_product->getProduct($product['product_id']);
				if (isset($gambar['image'])) {
					$image_path = str_replace(" ", "%20", $gambar['image']);
					$image_path = str_replace(".png", "", $image_path);
					$product['image'] = $base_url . $image_path . $suffix;
				} else {
					$product['image'] = null;
				}
			}
	
			if (!empty($filtered_products)) {
				$result[$order_id] = array(
					'transaksi' => array(
						'order_id' => $order_id,
						'date_added' => $formatted_date,
						'status_transaksi' => $status_transaksi,
						'class_transaksi' => $class_transaksi
					),
					'products' => $filtered_products
				);
			}
		}
	
		return $result;
	}

	public function getPemasukanAffiliate($affiliate_id){
		$query = $this->db->query("SELECT SUM(jumlah) AS total_saldo FROM oc_affiliate_pemasukan WHERE affiliate_id = '$affiliate_id'");
		return $query->row['total_saldo'];
	}

	public function getPengeluaranAffiliate($affiliate_id){
		$query = $this->db->query("SELECT SUM(jumlah) AS total_saldo FROM oc_affiliate_pengeluaran WHERE affiliate_id = '$affiliate_id'");
		return $query->row['total_saldo'];
	}

	public function getRiwayatPemasukan($affiliate_id){
		$query = $this->db->query("SELECT * FROM oc_affiliate_pemasukan WHERE affiliate_id = '$affiliate_id' ");
		return $query->rows;
	}

	public function getRiwayatPengeluaran($affiliate_id){
		$query = $this->db->query("SELECT * FROM oc_affiliate_pengeluaran WHERE affiliate_id = '$affiliate_id' ");
		return $query->rows;
	}

	public function getRiwayatSaldo($code){

		$this->load->model('affiliate/information');
        $affiliate_id = $this->model_affiliate_information->getIdAffiliate($code);
        $pemasukan = $this->model_affiliate_information->getRiwayatPemasukan($affiliate_id);
        $pengeluaran = $this->model_affiliate_information->getRiwayatPengeluaran($affiliate_id);

        $data = [];
        foreach ($pemasukan as $item) {
            $data[] = [
                "id" => $item["id_affiliate_pemasukan"],
                "tanggal" => $item["tanggal"],
                "jumlah" => $item["jumlah"],
                "keterangan" => "Pemasukan",
				"statuscss" => "text-success",
				"link" => "https://gudangmaterials.id/index.php?route=affiliate/detailsaldomasuk&id_status=",
				"status" => "",
				"warnastatus" => ""
            ];
        }

        foreach ($pengeluaran as $item) {
			if($item['status_penarikan'] == 2){
				$status = '(berhasil)';
				$warnastatus = 'text-success';
			}else if($item['status_penarikan'] == 3){
				$status = '(gagal)';
				$warnastatus = 'text-danger';
			}else {
				$status = '(diproses)';
				$warnastatus = 'text-secondary';
			}
            $data[] = [
                "id" => $item["id_affiliate_pengeluaran"],
                "tanggal" => $item["tanggal"],
                "jumlah" => $item["jumlah"],
                "keterangan" => "Pengeluaran",
				"statuscss" => "text-danger",
				"link" => "https://gudangmaterials.id/index.php?route=affiliate/statuspenarikan&id_status=",
				"status" => $status,
				"warnastatus" => $warnastatus
            ];
        }

        usort($data, function ($a, $b) {
            return strtotime($b["tanggal"]) - strtotime($a["tanggal"]);
        });

		return $data;

	}

	public function prosesTarikKomisi($jumlah){

		$this->load->model('affiliate/information');
		$affiliate = $this->model_affiliate_information->getProfile();
		$affiliate_id = $affiliate['affiliate_id'];
		$keterangan = 'Penarikan Komisi ' . $jumlah;

		$this->db->query(" INSERT INTO oc_affiliate_pengeluaran(affiliate_id, jumlah, keterangan, tanggal, status_penarikan) VALUES ('$affiliate_id', '$jumlah', '$keterangan', NOW(), '1')");

		$querypengeluaran = $this->db->query("SELECT * FROM oc_affiliate_pengeluaran ORDER BY id_affiliate_pengeluaran DESC LIMIT 1");
		$datapengeluaran = $querypengeluaran->row;
		$id_affiliate_pengeluaran = $datapengeluaran['id_affiliate_pengeluaran'];

		$querynotifadmin = $this->db->query("SELECT * FROM oc_affiliate_notifikasi_admin ORDER BY 	id_notifikasi_admin DESC LIMIT 1");
		$datanotifadmin = $querynotifadmin->row;
		$notif_id = ($datanotifadmin['id_notifikasi_admin'] + 1);

		$linkadmin = "https://gudangmaterials.id/index.php?route=adminaffiliate/transfer&id_affiliate_pengeluaran=". $id_affiliate_pengeluaran ."&status_notif=". $notif_id ."";

		$this->db->query("INSERT INTO oc_affiliate_notifikasi_admin SET tanggal=NOW(), keterangan='$keterangan', link='$linkadmin', status_baca='0' ");
	}

	public function getDetailPenarikan($id_penarikan){
		$query = $this->db->query("SELECT * FROM oc_affiliate_pengeluaran WHERE id_affiliate_pengeluaran='$id_penarikan' ");
		return $query->row;
	}

	public function getDetailPemasukan($id_penarikan){
		$query = $this->db->query("SELECT * FROM oc_affiliate_pemasukan WHERE id_affiliate_pemasukan='$id_penarikan' ");
		return $query->row;
	}

	public function updateStatusNotifikasiUserMasuk($id_affiliate_pemasukan){
		$this->db->query("UPDATE oc_affiliate_notifikasi_user SET status_baca='1' WHERE id_affiliate_pemasukan='$id_affiliate_pemasukan' ");
	}

	public function updateStatusNotifikasiUserKeluar($id_affiliate_pengeluaran){
		$this->db->query("UPDATE oc_affiliate_notifikasi_user SET status_baca='1' WHERE id_affiliate_pengeluaran='$id_affiliate_pengeluaran' ");
	}

	public function getJumlahNotifikasiUser($affiliate_id){
		$query = $this->db->query("SELECT * FROM oc_affiliate_notifikasi_user WHERE affiliate_id='$affiliate_id' AND status_baca='0'");
		$jumlah = count($query->rows);
	
		return $jumlah == 0 ? '' : ($jumlah > 9 ? '9+' : $jumlah);
	}
	

	public function getNotifikasiUser($affiliate_id){
		$query = $this->db->query("SELECT * FROM oc_affiliate_notifikasi_user WHERE affiliate_id='$affiliate_id' AND status_baca='0' ORDER BY id_affiliate_notifikasi_user DESC LIMIT 9");
		return $query->rows;
	}

	public function getAllNotifikasi($affiliate_id){
		$query = $this->db->query("SELECT * FROM oc_affiliate_notifikasi_user WHERE affiliate_id='$affiliate_id' ORDER BY id_affiliate_notifikasi_user DESC");
		return $query->rows;
	}
	

	// dashboard
	public function getTotalKlikByMonth($affiliate_id) {
		$currentMonth = date('m');
		$currentYear = date('Y');
	
		$query = $this->db->query("
			SELECT COUNT(*) AS total_klik 
			FROM oc_affiliate_klik 
			WHERE affiliate_id = '" . (int)$affiliate_id . "' 
			AND MONTH(tanggal) = '" . (int)$currentMonth . "' 
			AND YEAR(tanggal) = '" . (int)$currentYear . "'
		");
	
		return $query->row['total_klik'];
	}

	public function getTotalTransaksiByMonth($tracking) {
		$currentMonth = date('m');
		$currentYear = date('Y');
	
		$query = $this->db->query("
			SELECT COUNT(*) AS total_transaksi 
			FROM oc_order 
			WHERE tracking = '" . $this->db->escape($tracking) . "' 
			AND MONTH(date_added) = '" . (int)$currentMonth . "' 
			AND YEAR(date_added) = '" . (int)$currentYear . "'
		");
	
		return $query->row['total_transaksi'];
	}

	public function getJumlahTransaksiByMonth() {
		$this->load->model('affiliate/information');
		$this->load->model('catalog/product');
		$code = $this->affiliate->getCode();
		$transactions = $this->model_affiliate_information->getAllTransaction($code);
	
		$totalPrice = 0;
		$allowed_product_ids = array(614, 883, 334);
	
		foreach ($transactions as $transaction) {
			$order_id = $transaction['order_id'];
			$date_added = $transaction['date_added'];
	
			$currentMonth = date('m');
			$currentYear = date('Y');
			if (date('m', strtotime($date_added)) == $currentMonth && date('Y', strtotime($date_added)) == $currentYear) {
				$totalorder = $this->model_affiliate_information->getTotalProductTransaction($order_id);
	
				$filtered_products = array_filter($totalorder, function ($product) use ($allowed_product_ids) {
					return in_array($product['product_id'], $allowed_product_ids);
				});
	
				foreach ($filtered_products as $product) {
					$price = (int)$product['price'];
					$quantity = (int)$product['quantity'];
					$totalPrice += $price * $quantity;
				}
			}
		}
	
		return $totalPrice;
	}

	public function getJumlahKomisiByMonth() {
		$this->load->model('affiliate/information');
		$this->load->model('catalog/product');
		$code = $this->affiliate->getCode();
		$transactions = $this->model_affiliate_information->getAllTransaction($code);
	
		$totalKomisi = 0;
		$allowed_product_ids = array(614, 883, 334);
	
		foreach ($transactions as $transaction) {
			$order_id = $transaction['order_id'];
			$date_added = $transaction['date_added'];
	
			$currentMonth = date('m');
			$currentYear = date('Y');
			if (date('m', strtotime($date_added)) == $currentMonth && date('Y', strtotime($date_added)) == $currentYear) {
				$totalorder = $this->model_affiliate_information->getTotalProductTransaction($order_id);
	
				$filtered_products = array_filter($totalorder, function ($product) use ($allowed_product_ids) {
					return in_array($product['product_id'], $allowed_product_ids);
				});
	
				foreach ($filtered_products as $product) {
					$price = (int)$product['price'];
					$quantity = (int)$product['quantity'];
					$tarif_komisi = (int)$product['tarif_komisi'];
	
					$komisi = (($tarif_komisi / 100) * $price) * $quantity;
					$totalKomisi += $komisi;
				}
			}
		}
	
		return $totalKomisi;
	}

	public function getSalesProduct(){
		$query = $this->db->query("SELECT product_id, name, COUNT(*) AS quantity 
								   FROM oc_order_product 
								   WHERE product_id IN (334, 614, 883) 
								   GROUP BY name 
								   ORDER BY quantity DESC");
	
		$products = $query->rows;
	
		foreach ($products as &$product) {
			$gambar = $this->model_catalog_product->getProduct($product['product_id']);
			
			$image_name = $gambar['image'];
			
			if (!$image_name) {
				$image_name = 'default-image.png';
			}
	
			$image_name = str_replace(' ', '%20', $image_name); 
	
			$image_name = pathinfo($image_name, PATHINFO_FILENAME);
	
			$product['image_url'] = "https://gudangmaterials.id/image/cache/IDB/{$image_name}-500x500.png";
		}
	
		return $products;
	}

	public function getKomisiLast12Months() {
		$this->load->model('affiliate/information');
		$this->load->model('catalog/product');
		$code = $this->affiliate->getCode();
		$transactions = $this->model_affiliate_information->getTransactionSuccess($code);
	
		$komisi_per_bulan = array_fill(0, 12, 0);
		$allowed_product_ids = array(614, 883, 334);
	
		$bulan_nama = array(
			'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
			'Jul', 'Agus', 'Sep', 'Okt', 'Nov', 'Des'
		);
	
		$currentMonth = (int)date('m');
		$currentYear = (int)date('Y');
	
		foreach ($transactions as $transaction) {
			$order_id = $transaction['order_id'];
			$date_added = $transaction['date_added'];
	
			$transaction_month = (int)date('m', strtotime($date_added));
			$transaction_year = (int)date('Y', strtotime($date_added));
	
			$months_diff = ($currentYear - $transaction_year) * 12 + ($currentMonth - $transaction_month);
	
			if ($months_diff >= 0 && $months_diff < 12) {
				$totalorder = $this->model_affiliate_information->getTotalProductTransaction($order_id);
	
				$filtered_products = array_filter($totalorder, function ($product) use ($allowed_product_ids) {
					return in_array($product['product_id'], $allowed_product_ids);
				});
	
				foreach ($filtered_products as $product) {
					$price = (int)$product['price'];
					$quantity = (int)$product['quantity'];
					$tarif_komisi = (int)$product['tarif_komisi'];
	
					$komisi = (($tarif_komisi / 100) * $price) * $quantity;
	
					$index = (11 + $transaction_month - $currentMonth) % 12;
					$komisi_per_bulan[$index] += $komisi;
				}
			}
		}
	
		$komisi_dengan_bulan = array();
		$current_index = $currentMonth - 1;
		$current_year = $currentYear;
	
		for ($i = 11; $i >= 0; $i--) {
			$komisi_dengan_bulan[] = array(
				'bulan' => $bulan_nama[$current_index] . ' ' . $current_year,
				'komisi' => $komisi_per_bulan[$i]
			);
	
			$current_index--;
			if ($current_index < 0) {
				$current_index = 11;
				$current_year--;
			}
		}
	
		return array_reverse($komisi_dengan_bulan);
	}

	// admin

	public function getAllAfiliator() {
		$query = $this->db->query("SELECT * FROM oc_affiliate ORDER BY affiliate_id DESC");
		$results = $query->rows;
	
		// Map approved values to descriptive text
		foreach ($results as &$result) {
			switch ($result['approved']) {
				case "0":
					$result['approved'] = "belum diverifikasi";
					$result['status'] = "badge-warning";
					break;
				case "1":
					$result['approved'] = "verified";
					$result['status'] = "badge-success";
					break;
				case "2":
					$result['approved'] = "ditolak";
					$result['status'] = "badge-danger";
					break;
			}
		}
	
		return $results;
	}

	public function getDetailAfiliator($affiliate_id) {

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate` WHERE affiliate_id = '" . $affiliate_id . "'");
		return $query->row;
	}

	public function verifikasiData($affiliate_id){
		$this->db->query("UPDATE oc_affiliate SET approved='1' WHERE affiliate_id='$affiliate_id' ");
	}

	public function tolakVerifikasi($affiliate_id, $alasan){
		$this->db->query("UPDATE oc_affiliate SET approved='2', alasan_tolak='$alasan' WHERE affiliate_id='$affiliate_id' ");
	}

	public function getTransaksi() {
		$query = $this->db->query("
			SELECT 
				o.order_id, 
				FORMAT(o.total, 0) AS total, -- Format total
				os.name AS order_status, -- Ambil nama status pesanan
				DATE_FORMAT(o.date_added, '%d %M %Y') AS date_added, -- Format tanggal
				c.firstname, 
				c.lastname 
			FROM oc_order o
			JOIN oc_customer c ON o.seller_id = c.customer_id -- Gabungkan dengan tabel customer
			JOIN oc_order_status os ON o.order_status_id = os.order_status_id -- Gabungkan dengan tabel order status
			WHERE o.tracking IS NOT NULL AND o.tracking != ''
		");
	
		$result = [];
		foreach ($query->rows as $row) {
			$result[] = [
				'order_id' => $row['order_id'],
				'total' => $row['total'],
				'order_status' => $row['order_status'],
				'date_added' => $row['date_added'],
				'seller_name' => $row['firstname'] . ' ' . $row['lastname'],
			];
		}
	
		return $result;
	}

	public function pengajuanTarikDana() {
		$query = $this->db->query("
			SELECT 
				ap.id_affiliate_pengeluaran,
				ap.affiliate_id,
				ap.jumlah,
				ap.keterangan,
				ap.tanggal,
				ap.tanggal_pencairan,
				ap.status_penarikan,
				a.firstname,
				a.lastname,
				a.email
			FROM oc_affiliate_pengeluaran ap
			JOIN oc_affiliate a ON ap.affiliate_id = a.affiliate_id
			ORDER BY ap.tanggal DESC
		");
	
		$result = array();
		foreach ($query->rows as $row) {
			$statusMapping = array(
				'1' => array('status' => 'Transfer Sekarang', 'badge' => 'badge-warning'),
				'2' => array('status' => 'Sudah Ditransfer', 'badge' => 'badge-success'),
				'3' => array('status' => 'Gagal Ditransfer', 'badge' => 'badge-danger'),
			);
	
			$status = isset($statusMapping[$row['status_penarikan']]) 
				? $statusMapping[$row['status_penarikan']] 
				: array('status' => 'Tidak Diketahui', 'badge' => 'badge-secondary');
	
			$result[] = array(
				'id_affiliate_pengeluaran' => $row['id_affiliate_pengeluaran'],
				'affiliate_id' => $row['affiliate_id'],
				'jumlah' => $row['jumlah'],
				'keterangan' => $row['keterangan'],
				'tanggal' => $row['tanggal'],
				'tanggal_pencairan' => $row['tanggal_pencairan'],
				'status_penarikan' => $status['status'],
				'badge_class' => $status['badge'],
				'nama' => $row['firstname'] . ' ' . $row['lastname'],
				'email' => $row['email'],
			);
		}
	
		return $result;
	}

	public function getDataPengeluaran($id_affiliate_pengeluaran){
		$query = $this->db->query("SELECT * FROM oc_affiliate_pengeluaran WHERE id_affiliate_pengeluaran='$id_affiliate_pengeluaran' ");
		return $query->row;
	}

	public function updateTransferKomsi($id_affiliate_pengeluaran, $newFileName){
		$this->db->query("UPDATE oc_affiliate_pengeluaran SET tanggal_pencairan=NOW(), status_penarikan='2', bukti_transfer='$newFileName' WHERE id_affiliate_pengeluaran='$id_affiliate_pengeluaran' ");
	}

	public function addNotifikasiUserTransfer($id_affiliate_pengeluaran){

		$querypengeluaran = $this->db->query("SELECT * FROM oc_affiliate_pengeluaran WHERE id_affiliate_pengeluaran='$id_affiliate_pengeluaran' ");
		$datapengeluaran = $querypengeluaran->row;

		$querynotif = $this->db->query("SELECT * FROM oc_affiliate_notifikasi_user ORDER BY id_affiliate_notifikasi_user DESC LIMIT 1");
		$datanotif = $query->row;

		$id_notif_user = ($datanotif['id_affiliate_notifikasi_user'] + 1);
		$affiliate_id = $datapengeluaran['affiliate_id'];
		$jumlah = $datapengeluaran['jumlah'];
		$keterangan = "Penarikan " . $jumlah . " berhasil";
		$link = "https://gudangmaterials.id/index.php?route=affiliate/statuspenarikan&id_status=" . $id_affiliate_pengeluaran . "&status_notif=$id_notif_user";

		$this->db->query("INSERT INTO oc_affiliate_notifikasi_user SET affiliate_id='$affiliate_id', id_affiliate_pengeluaran='$id_affiliate_pengeluaran', tanggal=NOW(), keterangan='$keterangan', link='$link', status_baca='0' ");

		
	}

	public function getTotalAfiliator() {
		$query = $this->db->query("SELECT affiliate_id FROM oc_affiliate");
		return count($query->rows);
	}

	public function getTotalTransaksiAfiliator(){
		$query = $this->db->query("SELECT * FROM oc_order WHERE tracking IS NOT NULL AND tracking != '' ");
		return count($query->rows);
	}

	public function getJumlahTransaksiAfiliator() {
		$query = $this->db->query("SELECT total FROM oc_order WHERE tracking IS NOT NULL AND tracking != ''");
		$totalSum = 0;
	
		foreach ($query->rows as $row) {
			$totalSum += (int) $row['total'];
		}
	
		return $totalSum;
	}

	public function getJumlahTerjualProduk() {
		$query = $this->db->query("SELECT order_id FROM oc_order WHERE tracking IS NOT NULL AND tracking != ''");
	
		$produkJumlah = [];
	
		foreach ($query->rows as $row) {
			$order_id = $row['order_id'];
	
			$productQuery = $this->db->query("SELECT product_id, name, quantity FROM oc_order_product WHERE order_id = " . (int)$order_id);
	
			foreach ($productQuery->rows as $productRow) {
				if (!isset($produkJumlah[$productRow['product_id']])) {
					$produkJumlah[$productRow['product_id']] = [
						'name' => $productRow['name'],
						'quantity' => 0         
					];
				}
				$produkJumlah[$productRow['product_id']]['quantity'] += $productRow['quantity'];
			}
		}
	
		return $produkJumlah;
	}

	public function getTransaksi6Month() {
		$query = $this->db->query("SELECT total, date_added FROM oc_order WHERE tracking IS NOT NULL AND tracking != '' AND status_transaksi='5' ");
		$data = $query->rows;
	
		$result = [];
		$currentDate = new DateTime();
		for ($i = 0; $i < 6; $i++) {
			$month = $currentDate->format('Y-m');
			$monthName = $currentDate->format('F');
			$result[$month] = [
				'month_name' => $monthName,
				'total' => 0
			];
			$currentDate->modify('-1 month');
		}
	
		foreach ($data as $row) {
			$datetime = new DateTime($row['date_added']);
			$monthYear = $datetime->format('Y-m');
	
			$total = (int)$row['total'];
	
			if (isset($result[$monthYear])) {
				$result[$monthYear]['total'] += $total;
			}
		}
	
		return array_reverse($result);
	}

	// notifikasi

	public function getJumlahNotifikasiAdmin(){
		$query = $this->db->query("SELECT * FROM oc_affiliate_notifikasi_admin WHERE status_baca='0'");
		$jumlah = count($query->rows);
	
		return $jumlah == 0 ? '' : ($jumlah > 9 ? '9+' : $jumlah);
	}
	

	public function getNotifikasiAdmin(){
		$query = $this->db->query("SELECT * FROM oc_affiliate_notifikasi_admin WHERE status_baca='0' ORDER BY id_notifikasi_admin DESC LIMIT 9");
		return $query->rows;
	}

	public function getAllNotifikasiAdmin(){
		$query = $this->db->query("SELECT * FROM oc_affiliate_notifikasi_admin ORDER BY id_notifikasi_admin DESC");
		return $query->rows;
	}

	public function getKomisiMember(){
		$query = $this->db->query("SELECT * FROM oc_affiliate_member");
		return $query->rows;
	}

	// kirim email

	function sendMail($to, $subject, $htmlMessage, $textMessage = '', $from = null, $senderName = null) {
		// Akses ke konfigurasi OpenCart
		$config = $this->config; 
		
		// Inisialisasi Mail
		$mail = new Mail();
		$mail->protocol = $config->get('config_mail_protocol');
		$mail->parameter = $config->get('config_mail_parameter');
		$mail->smtp_hostname = $config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $config->get('config_mail_smtp_timeout');
	
		// Set data email
		$mail->setTo($to);
		$mail->setFrom($from ?: $config->get('config_email')); // Gunakan email default jika parameter $from kosong
		$mail->setSender(html_entity_decode($senderName ?: $config->get('config_name'), ENT_QUOTES, 'UTF-8')); // Gunakan nama default jika parameter $senderName kosong
		$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$mail->setHtml($htmlMessage);
		$mail->setText($textMessage);
	
		// Kirim email
		$mail->send();

		// Send to additional alert emails
		$emails = explode(',', $this->config->get('config_mail_alert'));

		foreach ($emails as $email) {
			if ($email && preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $email)) {
				$mail->setTo($email);
				$mail->send();
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		

}