<!DOCTYPE html>
<html>
	<head>
		<title>Maui Email Verification</title>
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
					style='padding:30px;'>
						<tr>
							<td align='center'>
                                <img width='40px' height='40px' src="{{ $message->embed(asset('favicon.png')) }}" />
                            </td>
						</tr>
						<tr>
							<td>
								<p style='margin:15px 0 12px 0;font-weight:bold; text-align:center;'>Maui Email Verification</p>
							</td>
						</tr>
						<tr>
							<td>
								<div style='line-height: 1.7; text-align:justify;'>
									Hello {{ explode(' ', $user->name, 2)[1] }}. Welcome to Maui, the trusted tool for keeping track of your expenses. One more step to complete
									your registration. Click the link below to verify your email.
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div style='margin:12px 0 2px 0'>
									<a href='{{ config("app.client_url") }}email/verify/{{ $user->activation_token }}' style='color:#000; font-size:1em;'>
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
                                        {{ config("app.client_url") }}email/verify/{{ $user->activation_token }}
                                    </p>
								</div>
							</td>
                        </tr>
                        <tr>
                            <td>
                                <div style='margin-top:15px'>
                                    <p style='margin:0;'>Regards,</p>
                                    <p style='margin:4px 0 0 0;'>Maui</p>
                                </div>
                            </td>
                        </tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>