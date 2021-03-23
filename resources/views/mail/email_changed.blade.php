<!DOCTYPE html>
<html>
	<head>
		<title>{{ config('app.name') }} Email Changed</title>
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
                            </td>
						</tr>
						<tr>
							<td>
								<p style='margin:15px 0 12px 0;font-weight:bold; text-align:center;font-size:16px;'>{{ config('app.name') }} Email Changed</p>
							</td>
						</tr>
						<tr>
							<td>
								<div style='line-height: 1.7; text-align:justify;'>
									Hey {{ $name }}. Your email has been changed successfully. From now on {{ $email }} is the email address associated with your 
                                    {{ config('app.name') }} account. If you have any more updates you would like to make, simply visit the settings page in the Maui app.
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