@extends('email.layout')
@section('content')
<td width="570" cellpadding="0" cellspacing="0" style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px;width:100%;margin:0;padding:0">
   <table width="100%" style="width:570px;margin:0 auto;padding:0;background-color:#ffffff">
      <tbody>
          <tr>
            <td style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px;padding:40px 30px">
                  <p style="font-style: normal; font-weight: 400; font-size: 16px; line-height: 24px; color: #7A8183; margin: 0; padding-bottom: 16px; text-align:left;"><?= __t('Hey there,', ['site' => config('app.name')]) ?></p>
      
                  <p style="font-style: normal; font-weight: 400; font-size: 16px; line-height: 24px; color: #7A8183; margin: 0; padding-bottom: 16px; text-align:left;"><?= __t('Click the button below to verify your email address.', ['site' => config('app.name')]) ?></p>
                  <a href="{{ $url }}" style="color:#ffffff;background-color:#000000;display:inline-block;text-decoration:none;border-radius:6px;box-sizing:border-box;font-weight:bold;margin-bottom:20px;" target="_blank">{{ __('Verify') }}</a>
                 
                  
                 <p style="font-style: normal; font-weight: 400; font-size: 16px; line-height: 24px; color: #7A8183; margin: 0; padding-bottom: 16px; text-align:left;"><?= __t('This verify link will expire in 60 minutes.', ['site' => config('app.name')]) ?></p>

                 <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #7A8183; margin: 0; padding-bottom: 16px; text-align:left;"><?= __t(
                  "If you're having trouble clicking the \"Verify\" button, copy and paste the URL below\n".
                  'into your web browser:') ?> <span class="break-all">{{ $url }}</span></p>
                 <p style="font-style: normal; font-weight: 400; font-size: 16px; line-height: 24px; color: #7A8183; margin: 0; padding-bottom: 16px; text-align:left;"><?= __t('Regards, <br> :site', ['site' => config('app.name')]) ?></p>
              </td>
          </tr>
      </tbody>
  </table>
 </td>
@stop