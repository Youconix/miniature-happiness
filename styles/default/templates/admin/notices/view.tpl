 <!-- admin view -->
<div id="notices" class="window">
    <table>
    <tbody>
    <tr>
	<td colspan="2" id="messageTitle">{title}</td>
    </tr>
    <tr>
	<td colspan="2" id="messageContent">{message}</td>
    </tr>
    <tr>
	<td colspan="2" id="messageDate">{date}</td>
    </tr>
    <tr>
	<td colspan="2"><input type="hidden" name="id" id="id" value="{messageId}"><br/></td>
    </tr>
    <tr>
	<td><input type="submit" name="delete" value="{buttonDelete}" onclick="deleteMessage({messageId})" id="buttonDelete"></td>
	<td><input type="submit" name="back" value="{buttonBack}" onclick="back()" id="buttonBack"></td>
   </tr>
   </tbody>
    </table>
</div>
