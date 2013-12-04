<div id="statsView">
	<h1>{pageTitle}</h1>
	
	<div class="adminPanel statsItem">
		<h2>{screenSizesTitle}</h2>
		
		<p><a href="javascript:adminStats.view2({month},{year})" class="button">{back}</a></p>
		
		<table>
		<tr>
			<td class="bold">{screenSize}</td>
			<td class="bold">{amount}</td>
		</tr>
		<block {screenSize}>
		<tr>
			<td>{name}</td>
			<td>{number}</td>
		</tr>
		</block>
		</table>
		
		<p><a href="javascript:adminStats.view2({month},{year})" class="button">{back}</a></p>
	</div>
</div>