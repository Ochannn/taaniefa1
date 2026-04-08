@include('master.tbowser')

<div class="modal fade" id="modalTambahData" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formTambahData">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data {{ $config['title'] }}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    @foreach ($formFields as $field)
                        <div class="form-group">
                            <label for="{{ $field['name'] }}">{{ $field['label'] }}</label>

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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

<button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#modalTambahData">
    Tambah Data
</button>

<div class="modal fade" id="modalEditData" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formEditData">
            @csrf
            <input type="hidden" id="edit_kode_lama">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data {{ $config['title'] }}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    @foreach ($formFields as $field)
                        <div class="form-group">
                            <label for="edit_{{ $field['name'] }}">{{ $field['label'] }}</label>

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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Data</button>
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
</script>