<style>
    html,
    body {
        padding: 0;
        margin: 0;
    }
</style>
<div
    style="font-family:Arial,Helvetica,sans-serif; line-height: 1.5; font-weight: normal; font-size: 15px; color: #2F3044; min-height: 100%; margin:0; padding:0; width:100%; background-color:#edf2f7">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="border-collapse:collapse;margin:0 auto; padding:0; max-width:600px">
        <tbody>
            <tr>
                <td align="center" valign="center" style="text-align:center; padding: 40px">
                    <a href="{{ config('app.url') }}" rel="noopener" target="_blank">
                        <img alt="Logo" src="{{ asset('img/icon.svg') }}" />
                    </a>
            <tr>
                <td align="left" valign="center">
                    <div
                        style="text-align:left; margin: 0 20px; padding: 40px; background-color:#ffffff; border-radius: 6px">
                        <!--begin:Email content-->
                        <div style="padding-bottom: 30px; font-size: 17px;">
                            <strong>
                                Hi {{ $notifiable->name }},
                            </strong>
                            Thank you for registering on our website.
                        </div>
                        <div style="padding-bottom: 30px; font-size: 17px;">
                            Please login on <a target="_blank" href="{{ route('login') }}">{{ route('login') }}</a>
                            and
                            fill up your profile.
                        </div>

                        <!--end:Email content-->
                        <div style="padding-bottom: 10px">Kind regards,
                            <br>The VPNSTORES Team.
            <tr>
                <td align="center" valign="center"
                    style="font-size: 13px; text-align:center;padding: 20px; color: #6d6e7c;">
                    <p>Indonesia</p>
                    <p>Copyright ©
                        <a href="{{ config('app.url') }}" rel="noopener" target="_blank">VPNSTORES</a>.
                    </p>
                </td>
            </tr></br>
</div>
</div>
</td>
</tr>
</img>
</a>
</td>
</tr>
</tbody>
</table>
</div>
