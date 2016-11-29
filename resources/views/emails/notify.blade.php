<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#e6e6e6" style="border:none;border-collapse:collapse">
    <tbody><tr height="50">
        <td style="border:none;border-collapse:collapse" align="center">
            <table>
                <tbody><tr>
                    <td height="50" width="100%" align="center"></td>
                </tr>
                </tbody></table>
        </td>
    </tr>
    <tr height="150">
        <td align="center">
            <table bgcolor="#ffffff" style="border:1px solid #cccccc;width:90%;max-width:600px;border:none;border-collapse:collapse">
                <tbody><tr>
                    <td height="20" width="100%" align="left" colspan="3" bgcolor="#ffb953"></td>
                </tr>
                <tr>
                    <td width="5%" align="center" bgcolor="#ffb953"></td>
                    <td align="left" width="90%" bgcolor="#ffb953">
                        <img src="https://ci4.googleusercontent.com/proxy/Uh801qqI4S9ux00f70wAN6uWEycNS_VNqPJPNUKMLkAd69ZjlnErLTBb7xHaKXrmVG21eIViLeDE-ZgCHV3PNm_qGA31G3hQAw=s0-d-e1-ft#http://www.matarebeli.ge/images/matarebeli_logo.png" alt="UniPAY" width="119" height="24" class="CToWUd">
                    </td>
                    <td width="5%" align="center" bgcolor="#ffb953"></td>
                </tr>
                <tr>
                    <td height="5" width="100%" align="left" colspan="3" bgcolor="#ffb953"></td>
                </tr>
                <tr>
                    <td width="5%" align="center" bgcolor="#ffb953"></td>
                    <td align="left" width="90%" bgcolor="#ffb953" style="color:#000000;font-size:13px;font-family:Gotham,'Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:300;line-height:16px"><i>შეიძინეთ მატარებლის ბილეთები ონლაინ</i></td>
                    <td width="5%" align="center" bgcolor="#ffb953"></td>
                </tr>
                <tr>
                    <td height="20" width="100%" colspan="3" align="center" bgcolor="#ffb953"></td>
                </tr>
                <tr>
                    <td height="20" width="100%" colspan="3" align="center"></td>
                </tr>
                <tr>
                    <td width="5%" align="center"></td>
                    <td align="left" width="90%" style="color:#202020;font-size:20px;font-family:Gotham,'Helvetica Neue',Helvetica,Arial,sans-serif;line-height:24px"></td>
                    <td width="5%" align="center"></td>
                </tr>
                <tr>
                    <td height="20" width="100%" align="left" colspan="3"></td>
                </tr>
                <tr>
                    <td width="5%" align="center"></td>
                    <td align="left" width="90%" style="color:#202020;font-size:14px;font-family:Gotham,'Helvetica Neue',Helvetica,Arial,sans-serif;line-height:16px">გმადლობთ რომ სარგებლობთ matarebeli.ge-ს მომსახურებით</td>
                    <td width="5%" align="center"></td>
                </tr>
                <tr>
                    <td height="20" width="100%" align="left" colspan="3"></td>
                </tr>
                <tr>
                    <td height="20" width="100%" align="center" colspan="3"></td>
                </tr>
                <tr>
                    <td width="5%" align="center"></td>
                    <td align="left" style="color:#202020;font-size:14px;font-family:Gotham,'Helvetica Neue',Helvetica,Arial,sans-serif;line-height:16px">თქვენს მიერ შეძენილი ბილეთ(ებ)ის შესყიდვის კოდი:</td>
                    <td width="5%" align="center"></td>
                </tr>
                <tr>
                    <td height="20" width="100%" align="left" colspan="3"></td>
                </tr>
                <tr>
                    <td width="5%" align="center"></td>
                    <td align="left" style="color:#202020;font-size:16px;font-family:Gotham,'Helvetica Neue',Helvetica,Arial,sans-serif;line-height:20px">
                        @foreach( $transaction->tickets as $key => $ticket )
                            @if( $key == 0 )
                                <p><strong>ერთი გზა {{$ticket->request_id}}</strong></p>
                            @else
                                <p><strong>მეორე გზა {{$ticket->request_id}}</strong></p>
                            @endif
                        @endforeach
                    </td>
                    <td width="5%" align="center"></td>
                </tr>
                <tr>
                    <td height="20" width="100%" align="center" colspan="3"></td>
                </tr>
                <tr>
                    <td width="5%" align="center"></td>
                    <td align="left" width="90%" style="color:#202020;font-size:14px;font-family:Gotham,'Helvetica Neue',Helvetica,Arial,sans-serif;line-height:16px">მიმაგრებულ ფაილში იხილეთ ბილეთის საბეჭდი ვერსია. <br><br>ელექტრონული სამგზავრო დოკუმენტით მგზავრობისათვის ვაგონის გამცილებელთან საჭიროა წარადგინოთ მგზავრების პიროვნების დამადასტურებელი მოწმობა.</td>
                    <td width="5%" align="center"></td>
                </tr>
                <tr>

                    <td height="20" width="100%" align="center" colspan="3"></td>
                </tr>
                <tr>
                    <td height="20" width="100%" align="left" colspan="3"></td>
                </tr>
                </tbody></table>
        </td>
    </tr>
    <tr height="50" width="100%">
        <td align="center">
            <table>
                <tbody><tr>
                    <td height="50" width="100%" align="center"></td>
                </tr>
                </tbody></table>
        </td>
    </tr>
    </tbody></table>