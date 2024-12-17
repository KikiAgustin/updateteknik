<?= $header; ?>
<?= $sidebar; ?>
<?= $navbar; ?>

<link href="<?= $template_assets; ?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800 mb-3">Detail Affiliator</h1>
    <form>
        <div class="form-group row">
            <label for="nama" class="col-sm-2 col-form-label">Nama Lengkap</label>
            <div class="col-sm-10">
                <input type="text" readonly class="form-control-plaintext" id="nama" value="<?= $afiliator['firstname'] ?> <?= $afiliator['lastname'] ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="email" readonly class="form-control-plaintext" id="email" value="<?= $afiliator['email'] ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="telepon" class="col-sm-2 col-form-label">Telepon</label>
            <div class="col-sm-10">
                <input type="text" readonly class="form-control-plaintext" id="telepon" value="<?= $afiliator['telephone'] ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
            <div class="col-sm-10">
                <input type="text" readonly class="form-control-plaintext" id="alamat" value="<?= $afiliator['address_1'] ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="kota" class="col-sm-2 col-form-label">Asal Kota</label>
            <div class="col-sm-10">
                <input type="text" readonly class="form-control-plaintext" id="kota" value="<?= $afiliator['city'] ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="provinsi" class="col-sm-2 col-form-label">Provinsi</label>
            <div class="col-sm-10">
                <input type="text" readonly class="form-control-plaintext" id="provinsi" value="<?= $afiliator['provinsi'] ?>">
            </div>
        </div>
                
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#verifikasimodal">
            Verifikasi Sekarang
        </button>
        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#tolakverifikasi">
            Tolak Verifikasi
        </button>

    </form>

</div>

<?= $footer; ?>

<script src="<?= $template_assets; ?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $template_assets; ?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script src="<?= $template_assets; ?>js/demo/datatables-demo.js"></script>

<!-- verfikasi modal -->
<div class="modal fade" id="verifikasimodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Verifikasi Data?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Yakin Mau Verifikasi Data Ini.</div>
                <form id="verifikasiForm" action="<?= $action; ?>" method="post">
                    <input type="hidden" name="affiliate_id" value="<?= $afiliator['affiliate_id']; ?>">
                    <input type="hidden" name="alasan" value="">
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <button id="submitButton" type="submit" class="btn btn-primary" >Verifikasi Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
</div>

<script>

	const form = document.getElementById('verifikasiForm');
	const submitButton = document.getElementById("submitButton");

	form.addEventListener('submit', function () {
		submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
		submitButton.disabled = true;
	});

</script>

    <!-- tolak verifikasi modal -->
<div class="modal fade" id="tolakverifikasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Verifikasi Ditolak?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span>Tulis Alasan Verifikasi Ditolak.</span>
                    <form id="tolakVerifikasiForm"  action="<?= $action; ?>" method="post">
                        <input type="hidden" name="affiliate_id" value="<?= $afiliator['affiliate_id']; ?>">
                        <div class="form-group">
                            <label for="alasan">Alasan Ditolak</label>
                            <textarea name="alasan" class="form-control" id="alasan" rows="3" required></textarea>
                        </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button id="tolakSubmitButton" type="submit" class="btn btn-primary">Tolak Verifikasi</button>
                </div>
                </form>
            </div>
        </div>
</div>

<script>

	const formTolak = document.getElementById('tolakVerifikasiForm');
	const tolakSubmitButton = document.getElementById("tolakSubmitButton");

	formTolak.addEventListener('submit', function () {
		tolakSubmitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
		tolakSubmitButton.disabled = true;
	});

</script>