<form style="margin:0;"><table>
<tr>
  <td><%buttontype%></td>
  <td><input type="radio" name="inc_mode" value="3" tabindex="120" checked="checked" id="btn_type_a" /><label for="btn_type_a">A: <%addtags%></label><br />
      <input type="radio" name="inc_mode" value="5"  id="btn_type_b" /><label for="btn_type_b">B: <%inserttext%></label></td>
</tr>
<tr>
  <td><%codebefore%></td>
  <td><input id="preadd" size="40" maxlength="160" value="" />(<%bothab%>)</td>
</tr>
<tr>
  <td nowrap><%codeafter%></td>
  <td><input id="postadd" size="40" maxlength="160" value="" />(<%aonly%>)</td>
</tr>
<tr>
  <td><%tip%></td>
  <td><input id="inputtitle" size="40" maxlength="160" value="" /></td>
</tr>
<tr>
  <td><%buttoncaption%></td>
  <td><input id="buttoncode" size="40" maxlength="160" value="" />(<%bothab%>)</td>
</tr>
<tr>
  <td colspan="2"><INPUT TYPE="button" VALUE="<%createcode%>" onclick="inserButtons()"><span id="so" style="color:red;"></span></td>
</tr>
<tr>
  <td colspan="2">
    <textarea cols="60" rows="12" id="inputcodes" ></textarea><br />
    <INPUT TYPE="button" VALUE="<%addbefore%>" onclick="reflectButtons(0)">
    <INPUT TYPE="button" VALUE="<%addafter%>" onclick="reflectButtons(1)">
  </td>
</tr>
</form></table>
