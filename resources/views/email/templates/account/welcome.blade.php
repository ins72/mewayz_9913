@extends('email.layout')
@section('content')
<td width="570" cellpadding="0" cellspacing="0" style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px;width:100%;margin:0;padding:0">
   <table width="100%" style="width:570px;margin:0 auto;padding:0;background-color:#ffffff">
      <tbody>
          <tr>
            <td style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px;padding:40px 30px">
                  <p style="font-style: normal; font-weight: 400; font-size: 16px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px; text-align:left;"><?= __t('Hey there,', ['site' => config('app.name')]) ?></p>

        
                  <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px;"><?= __t('We are thrilled to welcome you to <b>:site</b>! 🚀', ['site' => config('app.name')]) ?></p>
        
        
                  <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px;"><?= __t('Congratulations on taking the first step towards crafting a remarkable and engaging online presence using our intuitive AI-powered site builder. Whether you\'re a social media influencer, small business owner, artist, or an individual aiming to make a significant online impact, we are dedicated to assisting you in showcasing your story and content with ease and sophistication. With our various AI tools, creating stunning websites in under 5 minutes has never been easier.', ['site' => config('app.name')]) ?></p>
                  
                  <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px;"><?= __t('Log in to <b>:site</b> with your credentials to create your first site using AI. Personalize it with your sections and branding, and voilà! You\'re ready to publish it for your audience.', ['site' => config('app.name')]) ?></p>

                  <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px;"><?= __t('Thank you once again for choosing <b>:site</b>. We\'re excited to see the incredible sites you will create.', ['site' => config('app.name')]) ?></p>

                  <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 16px;"><?= __t('Best regards,', ['site' => config('app.name')]) ?></p>
                  <br>
                  <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 5px;"><?= __t('Support Team', ['site' => config('app.name')]) ?></p>
                  <p style="font-style: normal; font-weight: 400; font-size: 14px; line-height: 24px; color: #000000; margin: 0; padding-bottom: 0;"><?= __t('<b>:site</b>', ['site' => config('app.name')]) ?></p>

              </td>
          </tr>
      </tbody>
  </table>
 </td>
@stop