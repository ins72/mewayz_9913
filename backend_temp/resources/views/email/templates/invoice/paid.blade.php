@extends('email.layout')
@section('content')
<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%">
    <tbody>
        <tr>
            <td style="direction:ltr;font-size:0px;padding:20px 0 0 0;text-align:center">
                <div style="border-bottom:1px solid #f5f5f5;border-top:6px solid black;border-radius:7px 7px 0 0;background:#ffffff;margin:0px auto;max-width:582px">
                    <div style="background:#ffffff;width:100%">
                        <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;direction:ltr;font-size:0px;padding:0px;text-align:center">
                            <div style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
                                <div style="background-color:#ffffff;padding:0px;padding-top:20px;padding-bottom:20px;padding-left:20px">
                                    <div style="font-size:0px;padding:0px;word-break:break-word">
                                        <div style="width:140px">
                                            <a style="color:#2a9edb;font-weight:500;text-decoration:none" target="_blank">
                                                <img style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px" width="140" src="{{ gs('media/invoices', ao($invoice->payer, 'image')) }}">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div style="background:#ffffff;margin:0px auto;max-width:582px">
                        <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;direction:ltr;font-size:0px;padding:8px 30px;text-align:center">
                            <div style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
                                <div style="background-color:#ffffff;padding:0px">
                                    <div style="font-size:0px;padding:0px;word-break:break-word">
                                        <div style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:18px;text-align:left;color:#4c4c4c">
                                            <div style="font-size:24px;padding-bottom:12px;color:black;text-decoration:none">{{ ao($invoice->payer, 'name') }}</div>
                                            <div style="font-size:24px;padding-bottom:12px;color:black;text-decoration:none">{{ __('Brand Contact:') }} <a href="mailto:{{ ao($invoice->payer, 'email') }}" target="_blank">{{ ao($invoice->payer, 'email') }}</a></div>
                                            <div style="font-size:36px;color:black;padding-top:12px;padding-bottom:24px;font-weight:bold">{{ __('Amount:') }} {!! $invoice->user->price($invoice->price) !!}</div>
                                            <div style="color:#696969;font-size:14px">{{ __('Due Date:') }} {{ \Carbon\Carbon::parse($invoice->due)->toFormattedDateString() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div style="background:#ffffff;margin:0px auto;max-width:582px">
                        <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;direction:ltr;font-size:0px;padding:8px 30px;text-align:center">
                            <div style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
                                <div style="background-color:#ffffff;padding:10px 0px">
                                    <div style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:18px;text-align:left;color:#4c4c4c">
                                        {!! __t(':name just made a payment to this invoice and have been marked as paid. Click <a style="color:#2a9edb;font-weight:500;text-decoration:none" href=":link">here</a> to login to dashboard and view details.', [
                                            'name' => ao($invoice->payer, 'name'),
                                            'date' => \Carbon\Carbon::parse($invoice->due)->toFormattedDateString(),
                                            'link' => route('login')
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div style="border-radius:0 0 7px 7px;border-bottom:1px solid #f5f5f5;background:#ffffff;margin:0px auto;max-width:582px">
                        <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;direction:ltr;font-size:0px;padding:0px;text-align:center">
                            <div style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
                                <div style="background-color:#ffffff;padding:0px">
                                    <div style="font-size:0px;word-break:break-word">
                                        <div style="height:20px">&nbsp;</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
@stop
