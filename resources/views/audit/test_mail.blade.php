<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
	xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="x-apple-disable-message-reformatting">
	<title>Format for Audit Acknowledgement email</title>
	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,400i,700" rel="stylesheet" />

	<style>
		@media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
			u~div .email-container {
				min-width: 375px !important;
			}
		}

		@media only screen and (min-device-width: 414px) {
			u~div .email-container {
				min-width: 414px !important;
			}
		}

		.dashboard-button {
			font-weight: 800;
			font-size: 14px;
			padding: 10px;
			margin: 10px;
			background-color: #d8d8d8;
			border: none;
			color: #374e9e;
			font-family: 'Calibri', sans-serif;
		}

		.btn.btn-black-outline {
			border-radius: 0px;
			background: transparent;
			font-weight: 500;
			color: #374e9e;
		}

		.line_rating {
			border: 0px solid;
			border-image-slice: 1;
			border-bottom: 5px;
			border-image-source: linear-gradient(270deg, #364c9d, #ff0057);
		}

		.email-container {
			border-right: 0px solid;
			border-left: 0px solid;
			border-image-slice: 1;
			border-width: 0px;
			line-height: 30px;
			font-size: 10px;
			text-align: justify;
			border-image-source: linear-gradient(to left, #364c9d, #FF0057);
			font-family: 'Calibri', sans-serif;
			color: rgba(0, 0, 0, 0.877);
		}

		.g-id {
			margin: auto;
			padding: 0 2.0em;
			font-family: 'Calibri', sans-serif;
			text-align: left;
			background-color: #d8d8d8;
			width: 400px;
		}

		table,
		th,
		td {
			border: 1px solid black;
  			border-collapse: collapse;
			text-align: left; /* Default alignment */
		}
		
    </style>

	</style>
</head>

<body>

	<center style="width: 100%; background-color: white">

		<div style="max-width: 950px; margin: 0 auto;" class="email-container">

			<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
				style="margin: auto;">
				<tr>
					<td valign="top" class="bg_white" style="padding: 1em 2.5em 0 2.5em;">

						<hr class="line_rating">
						<div class="text" style="padding: 2em 0.5em; text-align: left;">

							<strong style="font-family: 'Calibri', sans-serif;font-size:14px;">Hi {{$audit->collectionManagerData->name}},</strong>
							<br/>
							<br/>
							<div>
								<div class="text" style="padding: 0.5em; text-align: left;">
									<strong
										style="font-family: 'Calibri', sans-serif; font-size:14px;">Greetings from QDegrees !</strong>
								</div>

                                <br/>
								<strong style=" font-family: 'Calibri', sans-serif; padding-top: 20px; font-size:14px">This is to
									apprise you that schedule <strong style="color:#9D1D27;">{{ucfirst($audit->qmsheet->type)}}</strong> audit has
									been successfully performed on following parameters..</strong>
	                            <br/>
								<table style="width:100%; font-family:'Calibri', sans-serif, monospace; font-size:14px">
									<tr bgcolor="#9D1D27" style="color: white; text-align:left;">

										<th>Branch</th>
										<th>Audit Type</th>
										<th>Name of Branch/Agency/Yard</th>
										<th>Collection Manager Name</th>
										<th>Product</th>
										<th>Date of Audit Performed</th>

									</tr>
									<tr>
									    <?php 
									    $nameAudit="";
									    
									    if($audit->branchRepo && $audit->branchRepo->name != "" && !is_null($audit->branchRepo->name)) {
									        $nameAudit=$audit->branchRepo->name;
									    }
									    elseif($audit->agency && $audit->agency->name != "" && !is_null($audit->agency->name)) {
									        $nameAudit=$audit->agency->name;
									    }
									    elseif($audit->agencyRepo && $audit->agencyRepo->name != "" && !is_null($audit->agencyRepo->name)) {
									        $nameAudit=$audit->agencyRepo->name;
									    }
									    elseif($audit->yard && $audit->yard->name != "" && !is_null($audit->yard->name)) {
									        $nameAudit=$audit->yard->name;
									    }
									    else {
									        $nameAudit=$audit->branchnew->name;
									    }
									    
									    ?>
										<td>{{$audit->branchnew->name}}</td>
										<td>{{ucfirst($audit->qmsheet->type)}}</td>
										<td>{{$nameAudit}}</td>
										<td>{{$audit->collectionManagerData->name}}</td>
										<td>{{$audit->productnew->name}}</td>
                                        <td>{{$audit->audit_date_by_aud}}</td>
									</tr>
								
									
								</table>

							</div>
                            <br/>
							<div>

								<div class="text" style="padding: 0.5em; text-align: left;">
									<strong style="font-family: 'Calibri', sans-serif; font-size: 14px;">
										Key Observations:</strong>
								</div>
								<table style="width:100%; font-family:'Calibri', sans-serif, monospace; font-size:14px">
									<tr bgcolor="#9D1D27" style="color: white; text-align:left;">
										<th>Main Parameter</th>
										<th>Sub Parameter</th>
										<th>Observation Remarks</th>
									</tr>
									@if(count($audit_data) > 0)
									@foreach($audit_data as $ad)
									<tr>
										<td>{{$ad->parameter_detail->parameter}}</td>
                                        <td>{{$ad->sub_parameter_detail->sub_parameter}}</td>
										<td>{{$ad->remark}}</td>
									</tr>
									@endforeach
									@endif
									

								</table>
								<br>
								@if($audit->qmsheet->type == 'agency')
								<div class="text" style="padding: 0.5em; text-align: left;">
									<strong style="font-family: 'Calibri', sans-serif; font-size: 14px;">
										FOS Details:-</strong>
								</div>
								<table style="width:100%; font-family:'Calibri', sans-serif, monospace; font-size:14px">
									<tr bgcolor="#9D1D27" style="color: white; text-align:left;">
										<th>Location</th>
										<th>Agency Name</th>
										<th>Agency code</th>
										<th>Agent Name</th>
										<th>Agent SFDC ID</th>
										<th>Collection Manager</th>
										<th>Product</th>
										<th>DRA</th>
										<th>PVR</th>
										<th>Endo Card</th>
									</tr>
									<tr>
									    <td>{{$audit->agency->location}}</td>
									    <td>{{$nameAudit}}</td>
									    <td>{{$audit->agency->agency_id}}</td>
									    <td></td>
									    <td></td>
								        <td>{{$audit->collectionManagerData->name}}</td>
								        <td>{{$audit->productnew->name}}</td>
								        <td></td>
								        <td></td>
								        <td></td>
									</tr>
									<tr>
									    <td>{{$audit->agency->location}}</td>
									    <td>{{$nameAudit}}</td>
									    <td>{{$audit->agency->agency_id}}</td>
									    <td></td>
									    <td></td>
								        <td>{{$audit->collectionManagerData->name}}</td>
								        <td>{{$audit->productnew->name}}</td>
								        <td></td>
								        <td></td>
								        <td></td>
									</tr>
									<tr>
									    <td>{{$audit->agency->location}}</td>
									    <td>{{$nameAudit}}</td>
									    <td>{{$audit->agency->agency_id}}</td>
									    <td></td>
									    <td></td>
								        <td>{{$audit->collectionManagerData->name}}</td>
								        <td>{{$audit->productnew->name}}</td>
								        <td></td>
								        <td></td>
								        <td></td>
									</tr>
									<tr>
									    <td>{{$audit->agency->location}}</td>
									    <td>{{$nameAudit}}</td>
									    <td>{{$audit->agency->agency_id}}</td>
									    <td></td>
									    <td></td>
								        <td>{{$audit->collectionManagerData->name}}</td>
								        <td>{{$audit->productnew->name}}</td>
								        <td></td>
								        <td></td>
								        <td></td>
									</tr>
									<tr>
									    <td>{{$audit->agency->location}}</td>
									    <td>{{$nameAudit}}</td>
									    <td>{{$audit->agency->agency_id}}</td>
									    <td></td>
									    <td></td>
								        <td>{{$audit->collectionManagerData->name}}</td>
								        <td>{{$audit->productnew->name}}</td>
								        <td></td>
								        <td></td>
								        <td></td>
									</tr>
									
									

								</table>
								<br>
								<div class="text" style="padding: 0.5em; text-align: left;">
									<strong style="font-family: 'Calibri', sans-serif; font-size: 14px; ">
										COC Posters & Declaration:</strong>
								</div>
								<table style="width:100%;font-family:'Calibri', sans-serif, monospace; font-size:14px">
									<tr bgcolor="#9D1D27" style="color: white; text-align:left;">
										<th>Location</th>
										<th>Agency Name</th>
										<th>Agency code</th>
										<th>COC posters</th>
										<th>No. of COC posters</th>
										<th>Declaration Form</th>
										<th>Hoardings</th>
										
									</tr>
									<tr>
									    <td>{{$audit->agency->location}}</td>
									    <td>{{$nameAudit}}</td>
									    <td>{{$audit->agency->agency_id}}</td>
									    <td></td>
									    <td></td>
								        <td></td>
								        <td></td>
									</tr>
								
									

								</table>
                                <br/>
                                @endif
                                
								<div>
									<div class="text" style="padding: 0.5em; text-align: left;">
										<strong
											style="font-family: 'Calibri', sans-serif; color:red; font-size: 14px;">Note:-</strong>
									</div>
                                    
									<ul style="font-family:'Calibri', sans-serif, monospace; font-size:14px">
										<li>
											This is an audit acknowledgement email, and we have identified the shared gap/alert; we need your assistance in taking the necessary action on close looping to assure compliance.

										</li>
										<li>
										If you notice any differences in this acknowledgement email, please respond within 24 hours with appropriate artifacts for correction on <?=$audit->qa_qtl_detail->email; ?>

										</li>
										<li>
											Thank you for your help in completing the audit.

										</li>
										
									</ul>

									<hr class="line_rating">
								</div>

					</td>
				</tr>
			</table>


		</div>
	</center>
</body>

</html>