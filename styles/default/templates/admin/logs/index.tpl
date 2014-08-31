<section id="logs">
    <h1>{logHeader}</h1>
    
	<block {log}>
	<section class="logItem">
	 	<table>
	 	<thead>
	        <tr>
	            <td>{nameHeader}</td>
	            <td>{dateHeader}</td>
	            <td></td>
	            <td></td>
	            <td></td>
	        </tr>
	     </thead>
	     <tbody>
	        <block {{logtype}}>
	            <tr>
	            	<td>{name}</td>
	                <td>{logDate}</td>
	                <td><a href="javascript:adminLogs.viewLog('{name}')"><img src="{style_dir}images/icons/view.png" alt="{viewText}" title="{viewText}"></a></td>
	                <td><a href="javascript:adminLogs.deleteLog('{name}')"><img src="{style_dir}images/icons/delete.png" alt="{deleteText}" title="{deleteText}"></a></td>
	                <td><a href="javascript:adminLogs.downloadLog('{name}')"><img src="{style_dir}images/icons/download.png" alt="{downloadText}" title="{downloadText}"></a></td>
	            </tr>
	        </block>
	    </tbody>
	    </table>
	</section>
	</block>
</section>