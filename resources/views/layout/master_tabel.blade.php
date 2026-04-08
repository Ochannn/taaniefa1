<table id="{{ $tableId }}" class="display" >
    <thead>
        <tr>
            @foreach ($columns as $column)
                <th>{{ $column['label'] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                @foreach ($columns as $column)
                    <td>{{ $row->{$column['field']} ?? '' }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        new DataTable('#{{ $tableId }}');
    });
</script>