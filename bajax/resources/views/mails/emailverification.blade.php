@extends('layouts.mail')

@section('header')
Verify Email
@endsection

@section('body')
<h3>Hello !</h3>
<p>
    Please click the button below for verify
</p>
<table class="action" align="center" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <a href="{{ $url }}" class="button button-blue" target="_blank">Verify Email</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<p>
    if you can't use the button above, open the link below
</p>
<p>
    <a href="{{ $url }}">{{ $url }}</a>
</p>
@endsection

@section('footer')
BAJAX ORGANIZATION
@endsection
