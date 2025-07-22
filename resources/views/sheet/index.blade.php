<h4>All Data</h4>
<table class="table table-bordered">
    @foreach ($sheetData as $row)
        <tr>
            @foreach ($row as $cell)
                <td>{{ $cell }}</td>
            @endforeach
        </tr>
    @endforeach
</table>

<hr>

<h4>Last Row</h4>
<ul>
    @foreach ($lastRow as $cell)
        <li>{{ $cell }}</li>
    @endforeach
</ul>
