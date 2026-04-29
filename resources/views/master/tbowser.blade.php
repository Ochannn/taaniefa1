<div class="table-responsive master-table-wrapper">
    <table id="myTable" class="table table-bordered table-hover master-table">
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $column['label'] }}</th>
                @endforeach
                <th width="150" class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($columns as $column)
                        <td>{{ $row->{$column['field']} ?? '-' }}</td>
                    @endforeach

                    <td class="text-center">
                        <button 
                            type="button"
                            class="btn btn-sm btn-outline-primary btn-edit"
                            data-id="{{ $row->{$primaryKey} }}"
                            @foreach ($editFields as $field)
                                data-{{ $field }}="{{ $row->{$field} }}"
                            @endforeach
                        >
                            Edit
                        </button>

                        <button 
                            type="button"
                            class="btn btn-sm btn-outline-danger btn-delete"
                            data-id="{{ $row->{$primaryKey} }}"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>