<div class="master-page">
    <div class="content-card master-card">
        <div class="master-top">
            <div>
                <h4 class="card-title">Data {{ $config['title'] }}</h4>
                <p class="card-subtitle">
                    Kelola data {{ strtolower($config['title']) }} pada tabel berikut.
                </p>
            </div>

            <button type="button" class="btn btn-primary btn-open-add-modal">
                <i class="fas fa-plus mr-1"></i>
                Tambah Data
            </button>
        </div>

        @include('master.tbowser')
    </div>
</div>

<div class="modal fade" id="modalTambahData" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formTambahData" class="w-100">
            @csrf

            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Data {{ $config['title'] }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    @foreach ($formFields as $field)
                        <div class="form-group">
                            <label>{{ $field['label'] }}</label>

                            @if($field['type'] === 'select')
                                <select
                                    class="form-control"
                                    id="{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    @if(empty($field['readonly'])) required @endif
                                >
                                    <option value="">Pilih {{ $field['label'] }}</option>

                                    @foreach (($fieldOptions[$field['options_key']] ?? []) as $option)
                                        <option value="{{ $option->value }}">
                                            {{ $option->text }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input
                                    type="{{ $field['type'] }}"
                                    class="form-control"
                                    id="{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    @if(!empty($field['readonly'])) readonly @endif
                                    @if(empty($field['readonly'])) required @endif
                                    @if($field['name'] === $primaryKey) value="{{ $nextKode }}" @endif
                                >
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Simpan Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditData" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formEditData" class="w-100">
            @csrf
            <input type="hidden" id="edit_kode_lama">

            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Data {{ $config['title'] }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    @foreach ($formFields as $field)
                        <div class="form-group">
                            <label>{{ $field['label'] }}</label>

                            @if($field['type'] === 'select')
                                <select
                                    class="form-control"
                                    id="edit_{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    @if($field['name'] !== $primaryKey) required @endif
                                >
                                    <option value="">Pilih {{ $field['label'] }}</option>

                                    @foreach (($fieldOptions[$field['options_key']] ?? collect()) as $option)
                                        <option value="{{ $option->value }}">
                                            {{ $option->text }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input
                                    type="{{ $field['type'] }}"
                                    class="form-control"
                                    id="edit_{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    @if($field['name'] === $primaryKey) readonly @endif
                                    @if($field['name'] !== $primaryKey) required @endif
                                >
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Update Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
window.masterConfig = {
    indexUrl: "{{ route('ajax.master.index', ['module' => $module]) }}",
    storeUrl: "{{ route('ajax.master.store', ['module' => $module]) }}",
    updateUrl: "{{ route('ajax.master.update', ['module' => $module, 'id' => ':id']) }}",
    deleteUrl: "{{ route('ajax.master.delete', ['module' => $module, 'id' => ':id']) }}",
    csrfToken: "{{ csrf_token() }}",
    addFormSelector: '#formTambahData',
    editFormSelector: '#formEditData',
    addModalSelector: '#modalTambahData',
    editModalSelector: '#modalEditData',
    editKeySelector: '#edit_kode_lama',
    autoCodeSelector: '#{{ $primaryKey }}',
    nextKode: "{{ $nextKode }}"
};

if (typeof window.initMasterPage === 'function') {
    window.initMasterPage();
}
</script>