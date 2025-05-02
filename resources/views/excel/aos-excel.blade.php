<table>
    <thead>
        <tr>
            <th>Period</th>
            <th>A/C In Fleet</th>
            <th>Flying Hours Total</th>
            <th>Revenue Take Off</th>
            <!-- tambahkan kolom lain sesuai kebutuhan -->
        </tr>
    </thead>
    <tbody>
        @foreach($reportData as $period => $data)
            <tr>
                <td>{{ $period }}</td>
                <td>{{ $data['acInFleet'] }}</td>
                <td>{{ $data['flyingHoursTotal'] }}</td>
                <td>{{ $data['revenueTakeOff'] }}</td>
                <!-- tambahkan kolom lain sesuai kebutuhan -->
            </tr>
        @endforeach
    </tbody>
</table>
