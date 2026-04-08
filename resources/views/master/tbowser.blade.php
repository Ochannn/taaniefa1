<table id="myTable" class="display">
    <thead>
        <tr>
            @foreach ($columns as $column)
                <th>{{ $column['label'] }}</th>
            @endforeach
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                @foreach ($columns as $column)
                    <td>{{ $row->{$column['field']} ?? '-' }}</td>
                @endforeach
                <td>
                    <button 
                        type="button"
                        class="btn btn-outline-primary btn-sm btn-edit"
                        data-id="{{ $row->{$primaryKey} }}"
                        @foreach ($editFields as $field)
                            data-{{ $field }}="{{ $row->{$field} }}"
                        @endforeach
                    >
                        Edit
                    </button>

                    <button 
                        type="button"
                        class="btn btn-outline-danger btn-sm btn-delete"
                        data-id="{{ $row->{$primaryKey} }}"
                    >
                        Delete
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>