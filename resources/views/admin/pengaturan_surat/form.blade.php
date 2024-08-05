@include('admin.layouts.components.asset_validasi')
@include('admin.layouts.components.asset_datatables')
@include('admin.layouts.components.jquery_ui')

@extends('admin.layouts.index')

@section('title')
<h1>
    Daftar Surat
    <small>{{ $action }} Pengaturan Surat</small>
</h1>
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('surat_master') }}">Daftar Surat</a></li>
<li class="active">{{ $action }} Pengaturan Surat</li>
@endsection

@section('content')
@include('admin.layouts.components.notifikasi')

{!! form_open($formAction, 'id="validasi" enctype="multipart/form-data"') !!}
<input type="hidden" id="id_surat" name="id_surat" value="{{ $suratMaster->id }}">
<div class="nav-tabs-custom">
    <div class="container-fluid identitas-surat">
        <h4>Surat {{ $suratMaster->nama ?? '' }}</h4>
    </div>
    <ul class="nav nav-tabs" id="tabs">
        <li class="active"><a href="#pengaturan-umum" data-toggle="tab">Umum</a></li>
        <li><a href="#template-surat" data-toggle="tab">Template</a></li>
        <li><a href="#form-isian" data-toggle="tab">Form Isian</a></li>
    </ul>
    <div class="tab-content">

        @include('admin.pengaturan_surat.umum')

        @if (in_array($suratMaster->jenis, [1, 2]))
            @include('admin.pengaturan_surat.rtf')
        @else
            @include('admin.pengaturan_surat.tinymce')
        @endif
        <div class="box-footer">
            <button type="reset" class="btn btn-social btn-danger btn-sm"><i class="fa fa-times"></i> Batal</button>
            @if (in_array($suratMaster->jenis, [1, 2]))
                <button type="submit" class="btn btn-social btn-info btn-sm pull-right"><i
                        class="fa fa-check"></i>Simpan</button>
            @else
                <button type="submit" name="action" class="btn btn-social btn-info btn-sm pull-right"><i
                        class="fa fa-check"></i>Simpan dan Keluar</button>
                <a onclick="formAction('validasi', '{{ $simpan_sementara }}')" id="konsep"
                    class="btn btn-social btn-warning btn-sm pull-right" style="margin: 0 8px 0 0;"><i
                        class="fa fa-file-code-o"></i>
                    Simpan Sementara</a>
                <button id="preview" name="action" value="preview"
                    class="btn btn-social btn-vk btn-success btn-sm pull-right" style="margin: 0 8px"><i
                        class="fa fa-eye"></i>Tinjau PDF</button>
            @endif
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
    <script>
        $('#validasi').submit(function () {
            tinymce.triggerSave()
        });

        $(document).ready(function () {
            syarat($('input[name=mandiri]:checked').val());
            $('input[name="mandiri"]').change(function () {
                syarat($(this).val());
            });

            $('#pengaturan-umum input[name=nama]').keyup(function (e) {
                $('div.identitas-surat h4').text('Surat ' + $(this).val())
            })

            $('#preview').click(function (e) {
                e.preventDefault();
                tinymce.triggerSave();

                Swal.fire({
                    title: 'Membuat pratinjau..',
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading()
                    },
                    allowOutsideClick: () => true
                });

                $.ajax({
                    url: `{{ route('surat_master/update_baru', $suratMaster->id) }}`,
                    type: 'POST',
                    data: $("#validasi").serialize() + "&action=preview",
                    success: function (response, status, xhr) {
                        // https://stackoverflow.com/questions/34586671/download-pdf-file-using-jquery-ajax
                        var filename = "";
                        var disposition = xhr.getResponseHeader('Content-Disposition');

                        console.log('disposition', xhr.getResponseHeader('Content-Disposition'))

                        if (disposition) {
                            var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                            var matches = filenameRegex.exec(disposition);
                            if (matches !== null && matches[1]) filename = matches[1].replace(
                                /['"]/g, '');
                        }
                        try {
                            var blob = new Blob([response], {
                                type: 'application/pdf'
                            });
                            if (typeof window.navigator.msSaveBlob !== 'undefined') {
                                //   IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                                window.navigator.msSaveBlob(blob, filename);
                            } else {
                                let jsonResponse = JSON.parse(response)

                                if (Object.keys(jsonResponse).length > 0) {
                                    const base64ori = jsonResponse.base64.trim().replace(/\s/g, "")
                                    const base64 = base64ori.split(`${jsonResponse.path}"`)[2]

                                    Swal.fire({
                                        customClass: {
                                            popup: 'swal-lg'
                                        },
                                        title: 'Pratinjau',
                                        html: `<object data="data:application/pdf;base64,${base64}" style="width: 100%;height: 80vh;" type="application/pdf"></object>`,
                                        showCancelButton: true,
                                        showConfirmButton: false,
                                        cancelButtonText: 'Tutup',
                                        allowOutsideClick: () => false
                                    })
                                }

                            }
                        } catch (ex) {
                            alert(ex); // This is an error
                        }
                    }
                }).fail(function (response, status, xhr) {
                    Swal.fire({
                        title: xhr.statusText,
                        icon: 'error',
                        text: response.statusText,
                    })
                })
            });
        });

        function masaBerlaku() {
            var masa_berlaku = $('#masa_berlaku').val();
            if (masa_berlaku < 0) {
                $('#masa_berlaku').val(0);
            } else if (masa_berlaku > 31) {
                $('#masa_berlaku').val(31);
            }
        }

        function syarat(tipe) {
            (tipe == '1' || tipe == null) ? $('#syarat').show() : $('#syarat').hide();
        }
    </script>
@endpush
