<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="telephone=no" name="format-detection">
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Heebo" rel="stylesheet"> -->
    <title></title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body
    style="background-color:#f1f1f1;padding:10px 0;margin:0;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size: 14px;">
    <table align="center" width="600" cellspacing="0" cellpadding="0"
        style="border-collapse:collapse;border-spacing:0px;padding:0;margin:auto;background: #fff;">
        @php
            $allocatedmodule = App\Helpers\Helper::allocatedmodulelist();
        @endphp
        <!-- Email Header -->
        <tr>
            <td style="padding: 20px 10px; border-bottom: 1px solid #ccc;">
                <table width="100%" cellspacing="0" cellpadding="0"
                    style="border-collapse:collapse;border-spacing:0px;">
                    <tr>
                        <td style="width: 50%; text-align: left;">
                            <img src="https://auditmitr.qdegrees.com/public/images/qdegrees.png" alt="QDegrees Logo"
                                width="150">
                        </td>
                        <td style="width: 50%; text-align: right;">
                            <img src="https://auditmitr.qdegrees.com/public/images/app_logo.png" alt="App Logo"
                                width="150">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <table cellspacing="0" cellpadding="0" align="center"
                    style="border-collapse:collapse;border-spacing:0px;width:100%">
                    <!-- <tr>
                        <td align="right" style="padding:20px 20px 0;font-size:0px"><img src="img/ub.png" alt=""
                                width="120"></td>
                    </tr>
                    <tr>
                        <td style="padding:30px 30px 30px;">
                            <table cellspacing="0" cellpadding="0"
                                style="border-collapse:collapse;border-spacing:0px;background-color:#ffffff;width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;">
                                <tr>
                                    <td align="left" style="text-decoration:none; font-weight:500;font-size:14px;">
                                        <img src="img/logo.svg" alt="" width="110">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr> -->
                    <tr>
                        <td align="left" style="padding:0px 30px 30px">
                            <table width="100%" cellspacing="0" cellpadding="0"
                                style="border-collapse:collapse;border-spacing:0px">
                                <tr>
                                    @if ($client_id == 74)
                                        <td style="padding:0 0 15px;line-height:21px;font-size:16px;font-weight: 600;">
                                            <b> Dear Team, </b>
                                        </td>
                                    @else
                                        <td style="padding:0 0 15px;line-height:21px;font-size:16px;font-weight: 600;">
                                            <b> Dear Associate, </b>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    @if ($client_id == 74)
                                        <td style="padding:0;line-height: 26px;">
                                            Greetings from QDegrees
                                        </td>
                                    @else
                                        <td style="padding:0;line-height: 26px;">
                                            Greetings from {{ $client_name }}
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td style="padding: 20px 0 0;">
                                        Thank you for your co-operation & assistance extended to the Process Reviewer in
                                        conducting the process review successfully.
                                    </td>
                                </tr>
                                @if ($client_id == 74)
                                    @if ($grade == 'A')
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                We wish to communicate the Process Review Rating for the month of
                                                {{ $audit_cycle }}, based on the review conducted in
                                                {{ date('F Y') }}.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                Congratulations on being graded as 'A' rated agency.
                                            </td>
                                        </tr>
                                    @elseif($grade == 'B')
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                We wish to communicate the Process Review Rating for the month of
                                                {{ $audit_cycle }}, based on the review conducted in
                                                {{ date('F Y') }}.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                Your agency has been graded as 'B'. We expect you to improve towards
                                                better
                                                performance.
                                            </td>
                                        </tr>
                                    @elseif($grade == 'C')
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                We wish to communicate the Process Review Rating for the month of
                                                {{ $audit_cycle }}, based on the review conducted in
                                                {{ date('F Y') }}.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                Your agency has been graded as 'C'. We expect you to improve towards
                                                better
                                                performance.
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                We wish to communicate the Process Review Rating for the month of
                                                {{ $audit_cycle }}, based on the review conducted in
                                                {{ date('F Y') }}.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                Your agency has been graded as 'D'.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                We suggest you improve on your performance and move towards 'A'.
                                            </td>
                                        </tr>
                                    @endif
                                @else
                                    @if ($grade == 'A')
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                We are glad to communicate the Process Review rating which was conducted
                                                in
                                                the month of {{ $audit_cycle }} at your agency. Congratulations on
                                                being
                                                graded as 'A' rated agency.
                                            </td>
                                        </tr>
                                    @elseif($grade == 'B')
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                We wish to communicate the Process Review rating which was conducted in
                                                the
                                                month of {{ $audit_cycle }} at your agency.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                Your agency has been graded as 'B'. We expect you to improve towards
                                                better
                                                performance.
                                            </td>
                                        </tr>
                                    @elseif($grade == 'C')
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                We wish to communicate the Process Review rating which was conducted in
                                                the
                                                month of {{ $audit_cycle }} at your agency.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                Your agency has been graded as 'C'. We expect you to improve towards
                                                better
                                                performance.
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                We wish to communicate the Process Review rating which was conducted in
                                                the
                                                month of {{ $audit_cycle }} at your agency.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                Your agency has been graded as 'D'.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 20px 0 0;">
                                                We suggest you improve on your performance and move towards 'A'.
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                                <tr>
                                    <td style="padding-top: 20px;">
                                        <table width="100%" spacing="0" cellspacing="0">
                                            <tr>
                                                <td width="25"
                                                    style="text-align: left;font-weight: bold;padding: 8px 10px;border: 1px solid #000;">
                                                    Name of the Audit Agency</td>
                                                <td width="75"
                                                    style="text-align: left;font-weight: normal;padding: 8px 10px;border: 1px solid #000;border-left: 0;">
                                                    {{ $audit_agency_name }}</td>
                                            </tr>
                                            <tr>
                                                <td width="25"
                                                    style="text-align: left;font-weight: bold;padding: 8px 10px;border: 1px solid #000;">
                                                    Name of the Agency</td>
                                                <td width="75"
                                                    style="text-align: left;font-weight: normal;padding: 8px 10px;border: 1px solid #000;border-left: 0;">
                                                    {{ $agency_name }}</td>
                                            </tr>
                                            <tr>
                                                <td width="25"
                                                    style="font-weight: normal;font-weight: bold;padding: 8px 10px;border: 1px solid #000;border-top: 0;">
                                                    Location</td>
                                                <td width="75"
                                                    style="font-weight: normal;padding: 8px 10px; text-align: left;border: 1px solid #000;border-top: 0;border-left: 0;">
                                                    {{ $location }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-weight: normal;font-weight: bold;padding: 8px 10px;border: 1px solid #000;border-top: 0;">
                                                    Entity</td>
                                                <td
                                                    style="font-weight: normal;padding: 8px 10px; text-align: left;border: 1px solid #000;border-top: 0;border-left: 0;">
                                                    {{ $client_name }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-weight: normal;font-weight: bold;padding: 8px 10px;border: 1px solid #000;border-top: 0;">
                                                    Product</td>
                                                <td
                                                    style="font-weight: normal;padding: 8px 10px; text-align: left;border: 1px solid #000;border-top: 0;border-left: 0;">
                                                    {{ $product_name }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-weight: normal;font-weight: bold;padding: 8px 10px;border: 1px solid #000;border-top: 0;">
                                                    Process Review Period</td>
                                                <td
                                                    style="font-weight: normal;padding: 8px 10px; text-align: left;border: 1px solid #000;border-top: 0;border-left: 0;">
                                                    {{ $audit_cycle }}</td>
                                            </tr>
                                            <tr>

                                                <td
                                                    style="font-weight: normal;font-weight: bold;padding: 8px 10px;border: 1px solid #000;border-top: 0;">
                                                    Process Reviewer Name</td>
                                                <td
                                                    style="font-weight: normal;padding: 8px 10px; text-align: left;border: 1px solid #000;border-top: 0;border-left: 0;">
                                                    {{ $client_id == 74 ? $auditor_name : $auditor_id }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-weight: normal;font-weight: bold;padding: 8px 10px;border: 1px solid #000;border-top: 0;">
                                                    Process Review Score</td>
                                                <td
                                                    style="font-weight: normal;padding: 8px 10px; text-align: left;border: 1px solid #000;border-top: 0;border-left: 0;">
                                                    @if (in_array(26, $allocatedmodule))
                                                        {{ $category_scores_per }}
                                                    @else
                                                        {{ $score_percentage }}
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td
                                                    style="font-weight: normal;font-weight: bold;padding: 8px 10px;border: 1px solid #000;border-top: 0;">
                                                    Process Review Rating</td>
                                                <td
                                                    style="font-weight: normal;padding: 8px 10px; text-align: left;border: 1px solid #000;border-top: 0;border-left: 0;">
                                                    {{ $rating_grade }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 0 0;">
                                        You may co-ordinate with your respective Collection Officer for any further
                                        queries.
                                    </td>
                                </tr>
                                @if ($grade == 'A')
                                    <tr>
                                        <td style="padding: 20px 0 0;">
                                            We look forward for your continuous support in this endeavour.
                                        </td>
                                    </tr>
                                @elseif($grade == 'B')
                                    <tr>
                                        <td style="padding: 20px 0 0;">
                                            We look forward for your continuous support in this endeavour.
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td style="padding: 20px 0 0;">
                                            The {{ $client_name }} may terminate the agreement if the rating remains
                                            further unsatisfactory.
                                        </td>
                                    </tr>
                                @endif
                                @if ($client_id == 249)
                                    <tr>
                                        <td style="padding: 25px 0 0;line-height: 24px;">
                                            <p style="font-weight: bold;">Note:</p>
                                            <p>1. A copy of the concern sheet updated with the Process Review
                                                observations is already available with you.</p>
                                            {{-- <p>2. Please note for all unsatisfactory points scanned image of the closure report and evidence is to be sent to the {{$client_name}}  <b>{{$manager_email}},{{$manager_email2}}, {{$client_email}} </b></p> --}}


                                            @if ($grade == 'C')
                                                <p>2. Ensure improvement on the unsatisfactory parameters by the next
                                                    Process review. </p>
                                                @if ($client_id != 15)
                                                    <p>3. To improve the unsatisfactory rating and get the latest
                                                        updates, kindly attend the monthly virtual training conducted by
                                                        the {{ $client_name }}. Also, Please refer the weekly mails
                                                        sent from the ID
                                                        <b>{{ $client_id == 15 ? $manager_email : $client_email }}
                                                        </b>for all recent updates in the process.
                                                    </p>
                                                @endif
                                            @elseif($grade == 'D')
                                                <p>2. Ensure improvement on the unsatisfactory parameters by the next
                                                    Process review. </p>
                                                @if ($client_id != 15)
                                                    <p>3. To improve the unsatisfactory rating and get the latest
                                                        updates, kindly attend the monthly virtual training conducted by
                                                        the {{ $client_name }}. Also, Please refer the weekly mails
                                                        sent from the ID
                                                        <b>{{ $client_id == 15 ? $manager_email : $client_email }}
                                                        </b>for all recent updates in the process.
                                                    </p>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td style="padding: 25px 0 0;line-height: 24px;">
                                            <p style="font-weight: bold;">Note:</p>
                                            <p>1. A copy of the concern sheet updated with the Process Review
                                                observations is already available with you.</p>
                                            {{-- <p>2. Please note for all unsatisfactory points scanned image of the closure report and evidence is to be sent to the {{$client_name}}  <b>{{$manager_email}},{{$manager_email2}}, {{$client_email}} </b></p> --}}
                                            <p>2. Please note for all unsatisfactory points scanned image of the closure
                                                report and evidence is to be sent to the {{ $client_name }} <b>
                                                    {{ $client_id == 74 ? 'piyali.banerjee@fibe.in, pradip.kumar@fibe.in & joicy.kunjuman@qdegrees.com' : implode(', ', array_filter([$manager_email, $client_email])) }}
                                                </b></p>


                                            @if ($grade == 'C')
                                                <p>3. Ensure improvement on the unsatisfactory parameters by the next
                                                    Process review. </p>
                                                @if ($client_id != 15)
                                                    <p>4. To improve the unsatisfactory rating and get the latest
                                                        updates, kindly attend the monthly virtual training conducted by
                                                        the {{ $client_name }}. Also, Please refer the weekly mails
                                                        sent from the ID
                                                        <b>{{ $client_id == 74 ? 'piyali.banerjee@fibe.in, pradip.kumar@fibe.in & joicy.kunjuman@qdegrees.com ' : $client_email }}
                                                        </b>for all recent updates in the process.
                                                    </p>
                                                @endif
                                            @elseif($grade == 'D')
                                                <p>3. Ensure improvement on the unsatisfactory parameters by the next
                                                    Process review. </p>
                                                @if ($client_id != 15)
                                                    <p>4. To improve the unsatisfactory rating and get the latest
                                                        updates, kindly attend the monthly virtual training conducted by
                                                        the {{ $client_name }}. Also, Please refer the weekly mails
                                                        sent from the ID
                                                        <b>{{ $client_id == 74 ? 'piyali.banerjee@fibe.in, pradip.kumar@fibe.in & joicy.kunjuman@qdegrees.com ' : $client_email }}
                                                        </b>for all recent updates in the process.
                                                    </p>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Email Footer -->
        <tr>
            <td style="padding: 15px 30px; border-top: 1px solid #ccc; background-color: #f9f9f9;">
                <table width="100%" cellspacing="0" cellpadding="0"
                    style="border-collapse:collapse;border-spacing:0px;">
                    <tr>
                        <td style="font-size: 11px; color: #666; text-align: left;">
                            Confidential
                        </td>
                        <td style="font-size: 11px; color: #666; text-align: right;">
                            © {{ date('Y') }} QDegrees Services. All rights reserved
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
</body>

</html>
