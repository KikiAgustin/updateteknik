<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gudang Material Affilate | Registrasi</title>
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <style>
      /* Ubah warna border dan fokus input */

      body {
      font-family: "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    }

      .form-control:focus {
        border-color: #db0f24;
        box-shadow: 0 0 0 0.2rem rgba(219, 15, 36, 0.25);
      }
      /* Ubah warna tombol */
      .btn-custom {
        background-color: #db0f24;
        border-color: #db0f24;
      }
      .btn-custom:hover {
        background-color: #a50b1b;
        border-color: #a50b1b;
      }
    </style>
  </head>
  <body>
    <div class="container my-5">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <h2 class="text-center mb-4">Form Registrasi</h2>

          <?php if ($error_warning) { ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <?php echo $error_warning; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php } ?>

          <form  id="registrationForm"  action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="needs-validation" >
            <!-- Informasi Pribadi -->
            <h4>Informasi Pribadi</h4>
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="firstName" class="form-label">Nama Depan</label>
                <input
                  type="text"
                  name="firstname"
                  value="<?php echo $firstname; ?>"
                  class="form-control"
                  id="firstName"
                  required
                />
                <?php if ($error_firstname) { ?>
                <div class="text-danger"><?php echo $error_firstname; ?></div>
                <?php } ?>
              </div>
              <div class="col-md-6">
                <label for="lastName" class="form-label">Nama Belakang</label>
                <input
                  type="text"
                  name="lastname"
                  value="<?php echo $lastname; ?>"
                  class="form-control"
                  id="lastName"
                  required
                />
                <?php if ($error_lastname) { ?>
                <div class="text-danger"><?php echo $error_lastname; ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" name="email" value="<?php echo $email; ?>" class="form-control" id="email" required />
              <?php if ($error_email) { ?>
              <div class="text-danger"><?php echo $error_email; ?></div>
              <?php } ?>
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Telepon</label>
              <input type="tel" name="telephone" value="<?php echo $telephone; ?>" class="form-control" id="phone" required />
              <?php if ($error_telephone) { ?>
              <div class="text-danger"><?php echo $error_telephone; ?></div>
              <?php } ?>
            </div>
            <input type="hidden" name="fax" value="" >
            <input type="hidden" name="company" value="" >
            <input type="hidden" name="website" value="" >
            <div class="mb-3">
              <label for="address" class="form-label">Alamat</label>
              <textarea
                class="form-control"
                name="address_1"
                id="address"
                rows="3"
                required
              ><?php echo $address_1; ?></textarea>
              <?php if ($error_address_1) { ?>
              <div class="text-danger"><?php echo $error_address_1; ?></div>
              <?php } ?>
            </div>
            <input type="hidden" name="address_2" value="" >
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="province" class="form-label">Provinsi</label>
                <input
                  type="text"
                  name="provinsi"
                  value="<?php echo $provinsi; ?>"
                  class="form-control"
                  id="province"
                  required
                />
                <?php if ($error_provinsi) { ?>
                <div class="text-danger"><?php echo $error_provinsi; ?></div>
                <?php } ?>
              </div>
              <div class="col-md-6">
                <label for="city" class="form-label">Kabupaten/Kota</label>
                <input type="text" name="city" value="<?php echo $city; ?>" class="form-control" id="city" required />
                <?php if ($error_city) { ?>
                <div class="text-danger"><?php echo $error_city; ?></div>
                <?php } ?>
              </div>
            </div>
            <input type="hidden" name="postcode" value="" >
            <input type="hidden" name="country_id" value="100" >
            <input type="hidden" name="zone_id" value="1515" >
            <input type="hidden" name="tax" value="" >
            <input type="hidden" name="payment" value="bank" >

            <!-- Informasi Pembayaran (collapse) -->
            <span>Klik untuk mengisi</span>
            <h4>
              <a
                class="text-decoration-none"
                data-bs-toggle="collapse"
                href="#paymentInfo"
                role="button"
                aria-expanded="false"
                aria-controls="paymentInfo"
              >
                Informasi Pembayaran (Opsional)
              </a>
            </h4>
            <div class="collapse" id="paymentInfo">
            <input type="hidden" name="cheque" value="" >
            <input type="hidden" name="paypal" value="" >
              <div class="mb-3">
                <label for="bankName" class="form-label">Nama Bank</label>
                <input type="text" name="bank_name" value="<?php echo $bank_name; ?>" class="form-control" id="bankName" />
              </div>
              <div class="mb-3">
                <label for="branch" class="form-label">Kantor Cabang</label>
                <input type="text" name="bank_branch_number" value="<?php echo $bank_branch_number; ?>"  class="form-control" id="branch" />
              </div>
              <input type="hidden" name="bank_swift_code" value="" >
              <div class="mb-3">
                <label for="accountName" class="form-label"
                  >Nama Rekening</label
                >
                <input type="text" name="bank_account_name" value="<?php echo $bank_account_name; ?>" class="form-control" id="accountName" />
              </div>
              <div class="mb-3">
                <label for="accountNumber" class="form-label"
                  >Nomor Rekening</label
                >
                <input type="text" name="bank_account_number" value="<?php echo $bank_account_number; ?>" class="form-control" id="accountNumber" />
              </div>
            </div>

            <!-- Kata Sandi -->
            <h4>Kata Sandi</h4>
            <label for="password" class="form-label">Password</label>
            <div class="mb-3 input-group">
              <input
                type="password"
                name="password"
                value="<?php echo $password; ?>"
                class="form-control"
                id="password"
                required
              />
              <span
                class="input-group-text"
                id="showPassword"
                onclick="togglePassword()"
              >
                <i class="bi bi-eye"></i>
              </span>
            </div>
              <?php if ($error_password) { ?>
              <div class="text-danger"><?php echo $error_password; ?></div>
              <?php } ?>
              <label for="confirmPassword" class="form-label"
                >Konfirmasi Password</label
              >
            <div class="mb-3 input-group">
              <input
                type="password"
                name="confirm"
                value="<?php echo $confirm; ?>"
                class="form-control"
                id="confirmPassword"
                required
              />
              <span
                class="input-group-text"
                id="showConfirmPassword"
                onclick="toggleConfirmPassword()"
              >
                <i class="bi bi-eye"></i>
              </span>
            </div>
            <?php if ($error_confirm) { ?>
              <div class="text-danger"><?php echo $error_confirm; ?></div>
            <?php } ?>

            <!-- Checkbox Syarat dan Ketentuan -->
            <div class="mb-3 form-check">
              <?php if ($agree) { ?>
              <input type="checkbox" name="agree" value="1" class="form-check-input" id="terms" checked="checked" required />
              <?php } else { ?>
                <input type="checkbox" name="agree" value="1" class="form-check-input" id="terms" required />
              <?php } ?>
              <label class="form-check-label" for="terms">
                Saya telah membaca dan setuju dengan
                <a href="/syarat-ketentuan" target="_blank"
                  >syarat dan ketentuan</a
                >
              </label>
            </div>

            <!-- Tombol Submit -->
            

            <div class="d-grid">
              <button id="submitButton" type="submit" class="btn btn-custom btn-danger text-white">
                <span id="buttonText">Daftar</span>
                <span id="buttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
          </form>

          <!-- Link Login -->
          <p class="text-center mt-3">
            Sudah memiliki akun?
            <a href="https://gudangmaterials.id/index.php?route=affiliate/login">Login melalui tautan berikut</a>.
          </p>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      // Fungsi untuk toggle visibility password
      function togglePassword() {
        var passwordField = document.getElementById("password");
        var icon = document.getElementById("showPassword").querySelector("i");
        if (passwordField.type === "password") {
          passwordField.type = "text";
          icon.classList.remove("bi-eye");
          icon.classList.add("bi-eye-slash");
        } else {
          passwordField.type = "password";
          icon.classList.remove("bi-eye-slash");
          icon.classList.add("bi-eye");
        }
      }

      // Fungsi untuk toggle visibility konfirmasi password
      function toggleConfirmPassword() {
        var confirmPasswordField = document.getElementById("confirmPassword");
        var icon = document
          .getElementById("showConfirmPassword")
          .querySelector("i");
        if (confirmPasswordField.type === "password") {
          confirmPasswordField.type = "text";
          icon.classList.remove("bi-eye");
          icon.classList.add("bi-eye-slash");
        } else {
          confirmPasswordField.type = "password";
          icon.classList.remove("bi-eye-slash");
          icon.classList.add("bi-eye");
        }
      }

      // Handle submit button state
      const form = document.getElementById('registrationForm');
      const submitButton = document.getElementById('submitButton');
      const buttonText = document.getElementById('buttonText');
      const buttonSpinner = document.getElementById('buttonSpinner');

      form.addEventListener('submit', function () {
        submitButton.classList.add('btn-loading');
        buttonText.classList.add('d-none');
        buttonSpinner.classList.remove('d-none');
        submitButton.disabled = true;
      });
    </script>

  </body>
</html>
