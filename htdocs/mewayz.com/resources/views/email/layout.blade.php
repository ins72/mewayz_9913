<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title></title>
        <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap');
        .table-body{
        background: #f6f6f6;
        background-color: #fff;
        width: 100%;
        }
        .table-container{
        display: block;
        margin: 0 auto !important;
        max-width: 600px;
        padding: 0px;
        width: 600px;
        }
        .table-main{
        background: #ffffff;
        border-radius: 3px;
        width: 100%;
        }
        .table-content{
        box-sizing: border-box;
        display: block;
        margin: 0 auto;
        max-width: 580px;
        padding: 4px;
        }
        p, ul, ol{
        font-family: sans-serif;
        font-size: 16px;
        font-weight: normal;
        margin: 0;
        line-height: 24px;
        margin-bottom: 15px;
        }
        .logo-container{
        padding: 60px 20px 20px;
        }
        .color-white{
        color: #fff;
        }
        .automate-message{
        font-size:12px;
        line-height:20px;
        text-align:left;
        color:#afafaf;
        }
        .main-wrapper table{
        width: 100%;
        }
        </style>
    </head>
    <body>
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;margin:0;padding:0;background-color:#f7f3f2">
            <tbody>
               <tr>
                  <td align="center" style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px">
                     <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;margin:0;padding:0">
                        <tbody>
                           <tr>
                              <td style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px;padding:25px 0;text-align:center">
                                 <a href="{{ config('app.url') }}" style="color:#3869d4" target="_blank">
                                 <img border="0" width="113" alt="{{ config('app.name') }}" src="{{ logo() }}" height="34" style="border:none" class="CToWUd" data-bit="iit">
                                 </a>
                              </td>
                           </tr>
                           <tr>
                            @yield('content')
                           </tr>
                           <tr>
                              <td style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px">
                                 <table class="m_4280045173277983148email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="width:570px;margin:0 auto;padding:0;text-align:center">
                                    <tbody>
                                       <tr>
                                          <td align="center" style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px;padding:40px 30px">
                                             <a href="{{ config('app.url') }}" style="color:#718096;text-decoration:underline" target="_blank">
                                             <img border="0" width="32" alt="{{ config('app.name') }}" src="{{ logo_icon() }}" height="37" style="border:none" class="CToWUd" data-bit="iit">
                                             </a>
                                             <p style="margin:0.4em 0 1.1875em;font-size:12px;line-height:1.625;color:#718096;text-align:center">{{ __('This is an automated message. You are receiving this email because you have a registered account with :site.', ['site' => config('app.name')]) }}</p>
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </td>
               </tr>
            </tbody>
         </table>
    </body>
</html>