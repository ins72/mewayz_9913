@extends('email.layout')
@section('content')
<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%">
    <tbody>
        <tr>
            <td style="direction:ltr;font-size:0px;padding:20px 0 0 0;text-align:center">
                <div style="margin:0px auto;max-width:582px">

                    <div style="border-bottom:1px solid #f5f5f5;border-top:6px solid black;border-radius:7px 7px 0 0;background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:582px">
                        <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;direction:ltr;font-size:0px;padding:0px;text-align:center">
                            <div class="m_-1098944197874829354mj-column-per-100" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
                                <div style="background-color:#ffffff;vertical-align:top;padding:0px;padding-top:20px;padding-bottom:20px;padding-left:20px">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:582px">
                        <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;direction:ltr;font-size:0px;padding:8px 30px;text-align:center">
                            <div class="m_-1098944197874829354mj-column-per-100" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
                                <div style="background-color:#ffffff;vertical-align:top;padding:0px">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:582px">
                        <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;direction:ltr;font-size:0px;padding:8px 30px;text-align:center">
                            <div class="m_-1098944197874829354mj-column-per-100" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
                                <div style="background-color:#ffffff;vertical-align:top;padding:0px">
                                    <div align="left" style="font-size:0px;padding:0px;word-break:break-word">
                                        <div style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:18px;text-align:left;color:#4c4c4c">
                                            <div style="color:black;font-size:24px">{{ __('Invoice from :name', ['name' => ao($invoice->data, 'name')]) }}</div>
                                            <div style="font-size:36px;color:black;padding-top:24px;padding-bottom:24px;font-weight:bold">{!! $invoice->user->price($invoice->price) !!}</div>
                                            <div style="color:#696969;font-size:14px">{{ __('Invoice Date:') }} {{ \Carbon\Carbon::parse($invoice->created_at)->toFormattedDateString() }}</div>
                                            <div style="color:#696969;font-size:14px">{{ __('Due Date:') }} {{ \Carbon\Carbon::parse($invoice->due)->toFormattedDateString() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:582px">
                        <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;direction:ltr;font-size:0px;padding:8px 30px;text-align:center">
                            <div class="m_-1098944197874829354mj-column-per-100" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
                                <p style="border-top:solid 1px #cbd5e0;font-size:1;margin:0px auto;width:100%">
                                </p>
                            </div>
                        </div>
                    </div>
                    <div style="background:#ffffff;margin:0px auto;max-width:582px">
                        <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;padding:8px 30px;text-align:center">
                            <div style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:18px;text-align:left;color:#4c4c4c">
                                <table style="width:100%">
                                    <tbody>
                                        <tr>
                                            <td style="padding-bottom:10px">
                                                <span style="color:#696969;font-size:14px">{{ __('To') }}</span>
                                            </td>
                                            <td style="padding-bottom:10px">
                                                <span style="color:black;font-size:14px">{{ ao($invoice->payer, 'name') }} ({{ ao($invoice->payer, 'email') }})</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span style="color:#696969;font-size:14px">{{ __('From') }}</span>
                                            </td>
                                            <td>
                                                <span style="color:black;font-size:14px">{{ ao($invoice->data, 'name') }} ({{ ao($invoice->data, 'email') }})</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:582px">
                        <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;direction:ltr;font-size:0px;padding:8px 30px;text-align:center">
                            <div class="m_-1098944197874829354mj-column-per-100" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
                                <p style="border-top:solid 1px #cbd5e0;font-size:1;margin:0px auto;width:100%">
                                </p>
                            </div>
                        </div>
                    </div>

                    <div style="background:#ffffff;margin:0px auto;max-width:582px">
                        <div style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:18px;text-align:left;color:#4c4c4c">
                            <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;padding:8px 30px;text-align:center">
                                <table style="width:100%;table-layout:fixed" width="100%">
                                    <tbody>
                                        <tr>
                                            <th style="text-align:left" align="left">
                                                <span style="color:black;font-size:14px">{{ __('Description') }}</span>
                                            </th>
                                            <th style="text-align:left" align="left">
                                                <span style="color:black;font-size:14px">{{ __('Amount') }}</span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left" align="left">
                                                <span style="color:#696969;font-size:14px">{{ ao($invoice->data, 'item_description') }}</span>
                                            </td>
                                            <td style="text-align:left" align="left">
                                                <span style="color:#696969;font-size:14px">{!! $invoice->user->price($invoice->price) !!}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table style="width:100%;margin-top:14px" width="100%">
                                    <tbody>
                                        <tr>
                                            <th style="text-align:left" align="left">
                                                <span style="color:black;font-size:14px">{{ __('Memo') }}</span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left" align="left">
                                                <span style="color:#696969;font-size:14px">
                                                    {{ ao($invoice->data, 'message') }}
                                                </span>
                                            </td>
                                        </tr>	
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:582px">
                        <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;direction:ltr;font-size:0px;padding:8px 30px;text-align:center">
                            <div class="m_-1098944197874829354mj-column-per-100" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
                                <p style="border-top:solid 1px #cbd5e0;font-size:1;margin:0px auto;width:100%">
                                </p>
                            </div>
                        </div>
                    </div>

                    <div style="background:#ffffff;margin:0px auto;max-width:582px">
                        <div align="center" bgcolor="#009940" role="presentation" valign="middle" style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;padding:8px 30px;text-align:center">
                            <a href="{{ route('out-invoice-single', ['slug' => $invoice->slug]) }}" style="display:inline-block;background:#000000;color:#ffffff;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:normal;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 20px;border-radius:4px">
                                {{ __('Pay Invoice') }}
                            </a>
                        </div>
                    </div> 
                    <div style="background:#ffffff;margin:0px auto;max-width:582px">
                        @php
                            $terms_link = settings('others.terms');
                            $privacy_link = settings('others.privacy');
                        @endphp
                        <p style="color:#696969;font-size:14px;margin:0px auto;width:100%">
                            {!! __t("By paying, I agree to our <a href=\"$terms_link\">Terms of service</a> and <a href=\"$privacy_link\">Privacy policy</a>.") !!}
                        </p>
                    </div>

                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:582px">
                        <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;direction:ltr;font-size:0px;padding:8px 30px;text-align:center">
                            <div class="m_-1098944197874829354mj-column-per-100" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
                                
                            </div>
                        </div>
                    </div>

                    <div style="background:#ffffff;margin:0px auto;max-width:582px">
                
                        {{-- <div style="background:#ffffff;margin:0px auto;max-width:582px">
                            <div style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:18px;text-align:left;color:#4c4c4c">
                                <div style="border-left:1px solid #f5f5f5;border-right:1px solid #f5f5f5;padding:8px 30px;text-align:center">
                                    <table style="width:100%" width="100%">
                                        <tbody>
                                            <tr><th style="text-align:left" align="left">
                                                <span style="color:black;font-size:14px">Banking Information</span>
                                            </th></tr>
                
                                            <!-- Banking information rows -->
                                            <tr><td><span style="color:#696969;font-size:14px"><strong>Beneficiary Name:</strong></span></td><td><span style="color:#696969;font-size:14px"> Jeff</span></td></tr>	
                                            <tr><td><span style="color:#696969;font-size:14px"><strong>Beneficiary Address:</strong></span></td><td><span style="color:#696969;font-size:14px">5 Bello Street ajebo lakowe</span></td></tr>	
                                            <tr><td></td><td><span style="color:#696969;font-size:14px">Lagos, LA, 2</span></td></tr>	
                                            <tr><td><span style="color:#696969;font-size:14px"><strong>Account Number:</strong></span></td><td><span style="color:#696969;font-size:14px"> 8104199676</span></td></tr>	
                                            <tr><td><span style="color:#696969;font-size:14px"><strong>Routing Number:</strong></span></td><td><span style="color:#696969;font-size:14px">324324323</span></td></tr>	
                                            <tr><td><span style="color:#696969;font-size:14px"><strong>Type of Account:</strong></span></td><td><span style="color:#696969;font-size:14px">Personal Savings</span></td></tr>	
                                            <tr><td><span style="color:#696969;font-size:14px"><strong>Bank Name:</strong></span></td><td><span style="color:#696969;font-size:14px">Rubies</span></td></tr>	
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> --}}
                
                        <!-- Payment Information -->
                
                        <!-- Footer -->
                        {{-- <div style="background:#ffffff;margin:0px auto;max-width:582px">
                            <div class="m_-1098944197874829354mj-column-per-100" style="">
                                <div align="center" class="" style="">
                                    <div class="" >
                                        Powered by 
                                        <a href="#" target="_blank" style="">
                                          <!-- Image placeholder -->
                                          Beacons
                                        </a>
                                    </div>   
                                </div>   
                            </div>   
                        </div>  --}}
                
                    </div>
                
                    <!-- Bottom spacer -->
                    <div class="" >
                        &nbsp;
                    </div>
                
                </div>
                
            </td>
        </tr>
    </tbody>
</table>
@stop
