
<p>Hi</p>

<p>Vouchers Receipt:</p>

<table>
    <thead>
        <tr>
            <th>Voucher Title</th>
            <th>Voucher Value</th>
            <th>Recipient Email</th>
            <th>Delivery Date</th>
            <th>Expiry Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($receipt_data as $receipt)
        <tr>
            <td>{{$receipt['voucher_title']}}</td>
            <td>{{$receipt['voucher_value']}}</td>
            <td>{{$receipt['recipient_email']}}</td>
            <td>{{$receipt['delivery_date']}}</td>
            <td>{{$receipt['expiry_date']}}</td>
        </tr>
        
        @endforeach
        <tr>
            <td>Total</td>
            <td>{{$total_value}}</td>
        </tr>
    </tbody>
</table>

<p>Please take note of all the important details like expiry date and terms of use.</p>

<p>Your voucher is also available in the validate app. If you have not downloaded the app you can do so here {link to app}. Log in with this email and access your voucher stored in the vault.</p>

<p>We hope you enjoy your vouchers</p>

<p>Regards</p>

<p>Validate</p>