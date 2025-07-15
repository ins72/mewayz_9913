@extends('email.layout')
@section('content')
<td width="570" cellpadding="0" cellspacing="0" style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px;width:100%;margin:0;padding:0">
   <table width="100%" style="width:570px;margin:0 auto;padding:0;background-color:#ffffff">
      <tbody>
          <tr>
            <td style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px;padding:40px 30px">
                  <p style="font-style: normal; font-weight: 400; font-size: 20px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px; text-align:left;"><?= __t(':email submitted a form on :site', [
                    'site' => $site->name,
                    'email' => ao($form->content, 'email')
                  ]) ?></p>

        
                  <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px;"><?= __t('Here are the details:') ?></p>
                

                <ul>
                    <li>
                        <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px;"><?= __t('First Name: :content', [
                            'content' => !empty(ao($form->content, 'first_name')) ? ao($form->content, 'first_name') : __('Not collected')
                        ]) ?></p>
                    </li>
                    <li>
                        <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px;"><?= __t('Last Name: :content', [
                            'content' => !empty(ao($form->content, 'last_name')) ? ao($form->content, 'last_name') : __('Not collected')
                        ]) ?></p>
                    </li>
                    <li>
                        <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px;"><?= __t('Email: :content', [
                            'content' => !empty(ao($form->content, 'email')) ? ao($form->content, 'email') : __('Not collected')
                        ]) ?></p>
                    </li>
                    <li>
                        <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px;"><?= __t('Phone: :content', [
                            'content' => !empty(ao($form->content, 'phone')) ? ao($form->content, 'phone') : __('Not collected')
                        ]) ?></p>
                    </li>
                    <li>
                        <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px;"><?= __t('Message: :content', [
                            'content' => !empty(ao($form->content, 'message')) ? ao($form->content, 'message') : __('Not collected')
                        ]) ?></p>
                    </li>
                </ul>

              </td>
          </tr>
      </tbody>
  </table>
 </td>
@stop