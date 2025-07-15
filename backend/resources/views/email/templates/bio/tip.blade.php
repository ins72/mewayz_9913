@extends('email.layout')
@section('content')
<td width="570" cellpadding="0" cellspacing="0" style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px;width:100%;margin:0;padding:0">
   <table width="100%" style="width:570px;margin:0 auto;padding:0;background-color:#ffffff">
      <tbody>
          <tr>
            <td style="font-family:&quot;Inter&quot;,Helvetica,Arial,sans-serif;font-size:16px;padding:40px 30px">
                <h1 style="box-sizing:border-box;font-family:'Inter','Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:600;line-height:1.5;margin-bottom:40px;font-size:24px;margin-top:0;text-align:center"><?= __('Woohoo! You got tipped!') ?></h1>
      
                  
                <div class="currency-payment is-price">
                    <div class="currency-field w-full">
                        <p style="box-sizing:border-box;font-family:'Inter','Helvetica Neue',Helvetica,Arial,sans-serif;font-size:60px;line-height:2;margin-bottom:40px;margin-top:0;color: #23262F;font-weight: 700;text-align: center;">

                            <strong style="box-sizing:border-box;font-family:'Inter','Helvetica Neue',Helvetica,Arial,sans-serif;font-size:16px;line-height:2;font-weight:600">{!! $currency !!}</strong> {{ $amount }}<br>
                        </p>
                    </div>
                </div>
                <p style="box-sizing:border-box;font-family:'Inter','Helvetica Neue',Helvetica,Arial,sans-serif;font-size:16px;line-height:2;margin-bottom:40px;margin-top:0;text-align:center; margin-top: 15px;">
                   {!! $description !!}
                </p>
              </td>
          </tr>
      </tbody>
  </table>
 </td>
@stop