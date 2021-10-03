<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <title>Cek Ongkir Raja Ongkir - M. Iqbal Effendi</title>
</head>
<body style="background-color: #e2e8f0">
<div class="container">
    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="card shadow mt-2">
                    <div class="card-body">
                        <h4 class="card-title text-center fw-bold">Lokasi</h4>
                        <hr>
                        <div class="form-group">
                            <label for="province_from" class="form-label">Provinsi Asal</label>
                            <input type="text" class="form-control" disabled value="DI Yogyakarta" id="province_from">
                        </div>
                        <div class="form-group my-4">
                            <label for="city_from" class="form-label">Kabupaten/Kota Asal</label>
                            <input type="text" class="form-control" disabled value="Kota Yogyakarta" id="city_from">
                        </div>
                        <div class="form-group">
                            <label for="province" class="form-label">Provinsi Tujuan</label>
                            <select class="form-select" id="province" name="province">
                                <option value="0" disabled selected>-- Pilih Provinsi --</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->province_id }}">{{ $province->province }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group my-4">
                            <label for="city" class="form-label">Kabupaten/Kota Tujuan</label>
                            <select class="form-select" id="city" name="city" disabled>
                                <option value="">-- Pilih Kabupaten/Kota --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card shadow mt-2">
                    <div class="card-body">
                        <h4 class="card-title text-center fw-bold">Pengiriman</h4>
                        <hr>
                        <div class="form-group">
                            <label for="courier" class="form-label">Jasa Pengiriman</label>
                            <select class="form-select" id="courier" name="courier">
                                @foreach ($couriers as $key => $val)
                                    <option value="{{ $key }}">{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group my-4">
                            <label for="weight" class="form-label">Berat (gram)</label>
                            <input type="number" class="form-control" id="weight" name="weight" min="0" value="0">
                        </div>
                        <div class="d-grid gap-2 my-4">
                            <button class="btn btn-primary" disabled type="button" id="submit">Cek Ongkir</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div id="result" class="mt-2">
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ"
        crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script>
    $(function () {
        $('#province').on('change', function () {
            const province_id = $(this).val();
            if (province_id) {
                $.ajax({
                    url: "{{ route('home') }}/city/" + province_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#city').prop('disabled', false).empty();
                        $.each(data, function (key, value) {
                            $('#city').append('<option value="' + value.city_id + '">' + value.type + ' ' + value.city_name + '</option>');
                        });
                        if ($('#weight').val() > 0) {
                            $('#submit').prop('disabled', false);
                        }
                    }
                });
            }
        });
        $('#city').on('change', function () {
            if ($('#weight').val() > 0) {
                $('#submit').prop('disabled', false);
            }
        });
        $('#weight').on('change', function () {
            if ($(this).val() > 0 && $('#city').val() > 0) {
                $('#submit').prop('disabled', false);
            }
        });
        $('#submit').on('click', function () {
            const _element = $(this);
            _element.prop('disabled', true).html('<div class="spinner-border text-sm" role="status"><span class="visually-hidden">Loading...</span></div>');
            const city = $('#city').val();
            const courier = $('#courier').val();

            const weight = $('#weight').val();
            if (city && courier && weight) {
                 const _container = $('#result');
                $.ajax({
                    url: "{{ route('home') }}/cost/" + courier + "/" + weight + "/" + city,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        const result = data[0];
                        _element.prop('disabled', false).html("Cek Ongkir");
                        if (result.code) {
                            let _card = `<div class="card shadow">
                                            <div class="card-header bg-primary text-white">
                                                ` + result.name + `
                                            </div>
                                            <ul class="list-group list-group-flush">`;
                            result.costs.forEach(e => {
                                console.log(e);
                                _card +=           `<li class="list-group-item">
                                                        ` + e.service + `
                                                        <div class="fw-light fs-6">`+e.description+`</div>
                                                        <ul>
                                                            <li>` + e.cost[0].etd + ` hari <span class="float-end fw-lighter">Rp`+e.cost[0].value+`</span> </li>
                                                        </ul>
                                                    </li>`;
                            });
                            _card +=       `</ul>
                                        </div>`;
                            _container.html(_card);
                        }
                    }
                });
            }
        });
    });
</script>
</body>
</html>
