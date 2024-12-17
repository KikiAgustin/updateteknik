<?= $header; ?>

<div class="container my-5">
            <!-- Heading -->
            <div class="text-center mb-5">
              <h1 class="h3">Konfirmasi Penarikan Komisi</h1>
              <p class="text-muted">
                Silakan konfirmasi detail penarikan Anda sebelum melanjutkan.
              </p>
            </div>

            <!-- Konfirmasi Penarikan -->
            <div class="row justify-content-center">
              <div class="col-md-6">
                <div class="card shadow-sm">
                  <div
                    class="card-header background-custom text-white text-center"
                  >
                    <h4>Konfirmasi Data Penarikan</h4>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <p>
                        <strong>Bank Tujuan:</strong>
                        <span id="konfirmasiBank"><?= $bankinfo['bank_name']; ?></span>
                      </p>
                      <p>
                        <strong>Nomor Rekening:</strong>
                        <span id="konfirmasiNomorRekening"><?= $bankinfo['bank_account_number']; ?></span>
                      </p>
                      <p>
                        <strong>Nama Rekening:</strong>
                        <span id="konfirmasiNamaRekening"><?= $bankinfo['bank_account_name']; ?></span>
                      </p>
                      <p>
                        <strong>Total Penarikan:</strong> Rp
                        <span id="konfirmasiJumlah"><?= number_format($jumlah, 0, '.', '.'); ?></span>
                      </p>
                    </div>

                    <div class="d-flex justify-content-between">
                      <a href="<?= $backwallet; ?>" class="btn btn-secondary" >Kembali</a>
                      <form id="penarikanForm"  action="<?= $this->url->link('affiliate/statuspenarikan', '', 'SSL'); ?>" method="post">
                      <input type="hidden" name="jumlah" value="<?= $jumlah; ?>" />
                      <input type="hidden" name="tanggal" value="<?= $tanggal; ?>" />
                      <button id="submitButton" type="submit" class="btn button-custom">Proses Penarikan</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

		  <script>

			const form = document.getElementById('penarikanForm');
			const submitButton = document.getElementById("submitButton");

			form.addEventListener('submit', function () {
				submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
				submitButton.disabled = true;
			});

		  </script>

<?= $footer; ?>