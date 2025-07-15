@extends('email.layout')
@section('content')
<td width="570" cellpadding="0" cellspacing="0" style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px;width:100%;margin:0;padding:0">
    <table class="m_4280045173277983148email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="width:570px;margin:0 auto;padding:0;background-color:#ffffff">
       <tbody>
          <tr>
             <td style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px;padding:40px 30px">
                <div>
                   <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" role="module">
                      <tbody>
                         <tr>
                            <td height="100%" valign="top" style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px">
                               <table width="100" cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="" class="m_4280045173277983148column">
                                  <tbody>
                                     <tr>
                                        <td style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px">
                                           <table role="module" border="0" cellpadding="0" cellspacing="0" width="100%">
                                              <tbody>
                                                 <tr>
                                                    <td valign="top" align="center" style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px">
                                                       <img border="0" style="margin-bottom:20px;border-radius:999px!important" width="64" alt="{{ $user->name }}" src="{{ $user->getAvatar() }}" height="64">
                                                    </td>
                                                 </tr>
                                              </tbody>
                                           </table>
                                        </td>
                                     </tr>
                                  </tbody>
                               </table>
                               <table width="360" cellpadding="0" cellspacing="0" align="left" border="0">
                                  <tbody>
                                     <tr>
                                        <td style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px">
                                           <table role="module" border="0" cellpadding="0" cellspacing="0" width="100%">
                                              <tbody>
                                                 <tr>
                                                    <td height="100%" valign="top" role="module-content" style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px">
                                                       <h2 style="margin-top:0;color:#333333;font-size:18px;font-weight:bold;text-align:left">{{ __('Invitation to join') }}</h2>
                                                       <p style="margin:0.4em 0 1.1875em;font-size:16px;line-height:1.625;color:#51545e">
                                                        {{ __(':name has invited you to join :workspace on :site.', [
                                                            'name' => $user->name,
                                                            'workspace' => $team->name,
                                                            'site' => config('app.name')
                                                           ]) }}
                                                          
                                                       </p>
                                                       <p style="margin:0.4em 0 1.1875em;font-size:16px;line-height:1.625;color:#51545e">{{ __('You\'ll be able to view, edit, and share decks for :workspace.', [
                                                        'workspace' => $team->name
                                                       ]) }}</p>
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
                         <tr>
                            <td align="center" style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px">
                               <a href="{{ route('dashboard-team-join', ['slug' => $team->slug]) }}" style="color:#ffffff;background-color:#2208cc;border-top:10px solid #2208cc;border-right:18px solid #2208cc;border-bottom:10px solid #2208cc;border-left:18px solid #2208cc;display:inline-block;text-decoration:none;border-radius:6px;box-sizing:border-box;font-weight:bold" target="_blank">{{ __('Join workspace') }}</a>
                            </td>
                         </tr>
                      </tbody>
                   </table>
                </div>
             </td>
          </tr>
       </tbody>
    </table>
 </td>
@stop