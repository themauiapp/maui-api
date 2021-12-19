<!DOCTYPE html>
<html>
	<head>
		<title>{{ config('app.name') }} Email Verification</title>
		<style type='text/css'>
			@import url('https://fonts.googleapis.com/css2?family=Quicksand&display=swap');
            
            .table__parent
            {
                padding:50px;
            }

			@media(max-width:610px)
			{
                .table__parent
                {
                    padding:10px;
                }
				.table__main
				{
					width:100%;
				}
			}
		</style>
	</head>
	<body style='margin:0; padding:0; font-family:"Quicksand", "Century Gothic", "Trebuchet MS", "Verdana", sans-serif; color:#000;'>
		<table width='100%' class='table__parent' cellpadding="0" cellspacing="0" style='background:#FBFBFB;font-size:1.1em;'>
			<tr>
				<td>
					<table width='600px' class='table__main' align="center" cellpadding="0" cellspacing="0" bgcolor="white"
					style='padding:30px;line-height: 1.7;'>
						<tr>
							<td align='center'>
                            </td>
						</tr>
						<tr>
							<td>
								<p style='margin:15px 0 12px 0;font-weight:bold; text-align:center;font-size:16px;'>{{ config('app.name') }} Email Verification</p>
							</td>
						</tr>
						<tr>
							<td>
								<div>
									Hello. Welcome to {{ config('app.name') }}, the trusted tool for keeping track of your expenses. One more step to complete
									your registration. Click the link below to verify your email.
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div style='margin:12px 0 2px 0'>
									<a href='{{ config("app.client_url") }}email/verify/{{ $url }}' style='color:#000; font-size:1em;'>
										{{ config('app.client_url') }}email/verify
									</a>									
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div style='margin-top:10px;'>
									Should you be unable to use the link above, simply copy and paste this link in your browser to verify your email.
								</div>
							</td>
                        </tr>
                        <tr>
							<td>
								<div style='margin-top:12px;'>
                                    <p style='margin:0; color:#000; font-size:1em; text-decoration:none;'>
                                        {{ config("app.client_url") }}email/verify/{{ $url }}
                                    </p>
								</div>
							</td>
                        </tr>
                        <tr>
                            <td>
                                <div style='margin-top:15px'>
                                    <p style='margin:0;'>Regards,</p>
                                    <p style='margin:4px 0 0 0;'>{{ config('app.name') }}</p>
                                </div>
                            </td>
                        </tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>