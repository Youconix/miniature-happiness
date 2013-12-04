<div id="logs">
    <h1>{logHeader}</h1>
    
	    <block {log}>
	    <div class="logItem adminPanel">
	    <table>
	        <tr>
	            <th>{nameHeader}</th>
	            <th>{dateHeader}</th>
	            <th></th>
	            <th></th>
	            <th></th>
	        </tr>
	        <block {{logtype}}>
	            <tr>
	            	<td>{name}</td>
	                <td>{logDate}</td>
	                <td><a href="javascript:adminLogs.viewLog('{name}')"><img src="{style_dir}images/icons/view.png" alt="{viewText}" title="{viewText}"/></a></td>
	                <td><a href="javascript:adminLogs.deleteLog('{name}')"><img src="{style_dir}images/icons/delete.png" alt="{deleteText}" title="{deleteText}"/></a></td>
	                <td><a href="javascript:adminLogs.downloadLog('{name}')"><img src="{style_dir}images/icons/download.png" alt="{downloadText}" title="{downloadText}"/></a></td>
	            </tr>
	        </block>
	    </table>
	    </div>
	    </block>
</div>