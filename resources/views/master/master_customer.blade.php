<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Data Customer</h5>
        </div>
        <div class="card-body">
            <form id="formCustomer" action="{{ route('customer.profile.update') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Nama Customer</label>
                    <input type="text" name="nama_customer" id="nama_customer" class="form-control" value="{{ $customer->nama_customer }}">
                    <div class="invalid-feedback" id="error_nama_customer"></div>
                </div>

                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="nohp_customer" id="nohp_customer" class="form-control" value="{{ $customer->nohp_customer }}">
                    <div class="invalid-feedback" id="error_nohp_customer"></div>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat_customer" id="alamat_customer" class="form-control" rows="4">{{ $customer->alamat_customer }}</textarea>
                    <div class="invalid-feedback" id="error_alamat_customer"></div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email_customer" id="email_customer" class="form-control" value="{{ $customer->email_customer }}">
                    <div class="invalid-feedback" id="error_email_customer"></div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('formCustomer').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    ['nama_customer', 'nohp_customer', 'alamat_customer', 'email_customer'].forEach(function(field) {
        document.getElementById(field).classList.remove('is-invalid');
        document.getElementById('error_' + field).innerText = '';
    });

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await response.json();

        if (!response.ok) {
            if (result.errors) {
                Object.keys(result.errors).forEach(function(field) {
                    const input = document.getElementById(field);
                    const error = document.getElementById('error_' + field);

                    if (input && error) {
                        input.classList.add('is-invalid');
                        error.innerText = result.errors[field][0];
                    }
                });
            } else {
                alert(result.message || 'Terjadi kesalahan.');
            }
            return;
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: result.message,
                timer: 1800,
                showConfirmButton: false
            });
        } else {
            alert(result.message);
        }
    } catch (error) {
        alert('Terjadi kesalahan saat menyimpan data.');
    }
});
</script>