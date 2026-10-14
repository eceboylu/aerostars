<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div class="text-muted small">
        <strong>{{ count($files) }}</strong> görsel
        @if (count($files) > 0)
            <span>({{ number_format($files->sum('size') / 1024, 0) }} KB)</span>
        @endif
    </div>

    <div class="d-flex gap-2">
        <button type="button" id="fm-delete-selected" class="btn btn-sm btn-outline-danger text-nowrap" disabled>Seçilenleri Sil</button>

        <form id="fm-upload-form" class="d-flex gap-2">
            @csrf
            <input type="file" id="fm-upload-input" name="image" accept="image/jpeg,image/png,image/webp" class="form-control form-control-sm" multiple required>
            <button type="submit" class="btn btn-sm btn-primary text-nowrap">Yükle</button>
        </form>
    </div>
</div>

<div id="fm-upload-error" class="text-danger small mb-2"></div>

<div id="fm-dropzone" class="border border-2 border-dashed rounded p-3">
    <p class="text-muted small text-center mb-3" id="fm-dropzone-hint">Görseli buraya sürükleyip bırakabilir veya yukarıdan se&ccedil;ebilirsiniz.</p>

    <div class="row g-2">
    @forelse ($files as $file)
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <div class="card h-100 position-relative">
                <input type="checkbox" class="form-check-input fm-select position-absolute m-2" data-name="{{ $file['name'] }}" aria-label="{{ $file['name'] }} seç">
                <img src="{{ $file['url'] }}" class="card-img-top" style="height:100px;object-fit:cover;" alt="{{ $file['name'] }}">
                <div class="card-body p-2">
                    <div class="small text-truncate" title="{{ $file['name'] }}">{{ $file['name'] }}</div>
                    @if ($picker)
                        <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-1 fm-pick" data-url="{{ $file['url'] }}">Seç</button>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 mt-1 fm-copy" data-url="{{ $file['url'] }}">Path'i Kopyala</button>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-danger w-100 mt-1 fm-delete" data-name="{{ $file['name'] }}">Sil</button>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">Henüz görsel yok.</p>
    @endforelse
    </div>
</div>

<script>
    var errorBox = document.getElementById('fm-upload-error');
    var dropzone = document.getElementById('fm-dropzone');
    var maxUploadBytes = 2 * 1024 * 1024;

    function formatMb(bytes) {
        return (bytes / (1024 * 1024)).toFixed(1);
    }

    function uploadOne(file) {
        var data = new FormData();
        data.append('_token', '{{ csrf_token() }}');
        data.append('image', file);

        return fetch('{{ route('admin.filemanager.upload') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: data,
        })
            .then(function (response) { return response.json(); })
            .then(function (result) { return !!result.path; })
            .catch(function () { return false; });
    }

    function uploadFiles(fileList) {
        errorBox.textContent = '';

        var files = Array.from(fileList);
        var failed = [];
        var uploaded = 0;

        function next(index) {
            if (index >= files.length) {
                if (failed.length) {
                    var names = failed.map(function (f) { return f.name; });
                    alert(failed.length + ' dosya yüklenemedi:\n' + names.join('\n'));
                    errorBox.textContent = failed.length + ' dosya yüklenemedi: ' + names.join(', ');
                }

                if (uploaded > 0) {
                    window.location.reload();
                }

                return;
            }

            var file = files[index];

            if (file.size > maxUploadBytes) {
                failed.push({ name: file.name + ' (' + formatMb(file.size) + ' MB, maksimum 2 MB)' });
                next(index + 1);
                return;
            }

            uploadOne(file).then(function (ok) {
                if (ok) {
                    uploaded++;
                } else {
                    failed.push({ name: file.name });
                }

                next(index + 1);
            });
        }

        next(0);
    }

    document.getElementById('fm-upload-form').addEventListener('submit', function (event) {
        event.preventDefault();

        var files = document.getElementById('fm-upload-input').files;

        if (files.length) {
            uploadFiles(files);
        }
    });

    ['dragenter', 'dragover'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropzone.classList.add('border-primary');
        });
    });

    ['dragleave', 'drop'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropzone.classList.remove('border-primary');
        });
    });

    dropzone.addEventListener('drop', function (event) {
        var files = event.dataTransfer.files;

        if (files.length) {
            uploadFiles(files);
        }
    });

    document.querySelectorAll('.fm-pick').forEach(function (button) {
        button.addEventListener('click', function () {
            window.opener.fmSelect(button.dataset.url);
            window.close();
        });
    });

    document.querySelectorAll('.fm-copy').forEach(function (button) {
        button.addEventListener('click', function () {
            navigator.clipboard.writeText(button.dataset.url);

            var original = button.textContent;
            button.textContent = 'Kopyalandı!';
            setTimeout(function () {
                button.textContent = original;
            }, 1000);
        });
    });

    function deleteFile(name) {
        return fetch('{{ url('/admin/filemanager') }}/' + encodeURIComponent(name), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        });
    }

    document.querySelectorAll('.fm-delete').forEach(function (button) {
        button.addEventListener('click', function () {
            if (!confirm('"' + button.dataset.name + '" silinsin mi?')) {
                return;
            }

            deleteFile(button.dataset.name).then(function () {
                button.closest('.col-6').remove();
            });
        });
    });

    var deleteSelectedButton = document.getElementById('fm-delete-selected');

    function updateDeleteSelectedButton() {
        var checked = document.querySelectorAll('.fm-select:checked');
        deleteSelectedButton.disabled = checked.length === 0;
        deleteSelectedButton.textContent = checked.length ? 'Seçilenleri Sil (' + checked.length + ')' : 'Seçilenleri Sil';
    }

    document.querySelectorAll('.fm-select').forEach(function (checkbox) {
        checkbox.addEventListener('change', updateDeleteSelectedButton);
    });

    deleteSelectedButton.addEventListener('click', function () {
        var checked = Array.from(document.querySelectorAll('.fm-select:checked'));

        if (!checked.length || !confirm(checked.length + ' görsel silinsin mi?')) {
            return;
        }

        function next(index) {
            if (index >= checked.length) {
                return;
            }

            var checkbox = checked[index];

            deleteFile(checkbox.dataset.name).then(function () {
                checkbox.closest('.col-6').remove();
                next(index + 1);
            });
        }

        next(0);
    });
</script>
