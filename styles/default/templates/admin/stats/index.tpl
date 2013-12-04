<div id="statsView">
	<h1>{pageTitle}</h1>
	
	<div class="adminPanel statsItemHits">
		<h2>{lastMonth} {HitsUniqueTitle} {nextMonth}</h2>
		
		<table>
		<tr>
			<block {visitorsDay}>
			<td class="bold">{day}</td>
			</block>
		</tr>
		<tr>
			<block {visitorsNumber}>
			<td>{number}</td>
			</block>
		</tr>
		</table>
	</div>
	
	<div class="adminPanel statsItemHits">
		<h2>{lastMonth} {HitsTitle} {nextMonth}</h2>
		
		<table>
		<tr>
			<block {hitsDay}>
			<td class="bold">{day}</td>
			</block>
		</tr>
		<tr>
			<block {hitsNumber}>
			<td>{number}</td>
			</block>
		</tr>
		<tr>
			<td colspan="2"><br/></td>
		</tr>
		</table>
		<table>
		<block {visitorsHour}>
		<tr>
			<td>{hour}</td>			
			<td>{number}</td>
			<td ><div style="background-color:#FFF; height:20px; width:{width}px"><br/></div></td>
			<td>{percent}%</td>
		</tr>
		</block>
		</table>
	</div>
	
	<div class="adminPanel statsItemHits">
		<h2>{pagesTitle}</h2>
		
		<table>
		<block {visitorsPage}>
		<tr>
			<td>{page}</td>			
			<td>{number}</td>
			<td ><div style="background-color:#FFF; height:20px; width:{width}px"><br/></div></td>
			<td>{percent}%</td>
		</tr>
		</block>
		</table>
	</div>
	
	<div class="adminPanel statsItem">
		<h2>{screenSizesTitle}</h2>
		
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
		
		<p><a href="javascript:adminStats.viewSizes({month},{year})" class="button">{fullList}</a></p>
	</div>
	
	<div class="adminPanel statsItem">
		<h2>{operatingTitle}</h2>
		
		<table>
		<tr>
			<td class="bold">{osTitle}</td>
			<td class="bold">{amount}</td>
		</tr>
		<block {OS}>
		<tr>
			<td>{name}</td>
			<td>{number}</td>
		</tr>
		</block>
		</table>
		
		<p><a href="javascript:adminStats.viewOS({month},{year})" class="button">{fullList}</a></p>
	</div>
	
	<div class="adminPanel statsItem">
		<h2>{browsersTitle}</h2>
		
		<table>
		<tr>
			<td class="bold">{browserTitle}</td>
			<td class="bold">{amount}</td>
		</tr>
		<block {browser}>
		<tr>
			<td>{name}</td>
			<td>{number}</td>
		</tr>
		</block>
		</table>
		
		<p><a href="javascript:adminStats.viewBrowsers({month},{year})" class="button">{fullList}</a></p>
	</div>
	
	<div class="adminPanel statsItem">
		<h2>{screenColorsTitle}</h2>
		
		<table>
		<tr>
			<td class="bold">{colorTitle}</td>
			<td class="bold">{amount}</td>
		</tr>
		<block {screenColor}>
		<tr>
			<td>{name}</td>
			<td>{number}</td>
		</tr>
		</block>
		</table>
	</div>
	
	<div class="adminPanel statsItem">
		<h2>{referencesTitle}</h2>
		
		<table>
		<tr>
			<td class="bold"><br/></td>
			<td class="bold">{amount}</td>
			<td><br/></td>
		</tr>
		<block {reference}>
		<tr>
			<td>{name}</td>
			<td>{number}</td>
			<td>{percent}%</td>
		</tr>
		</block>
		</table>
	</div>
	
	<div class="adminPanel statsItem">
		<h2>{domainsTitle}</h2>
		
		<table>
		<tr>
			<td class="bold"><br/></td>
			<td class="bold">{amount}</td>
			<td colspan="2" ><br/></td>
		</tr>
		<block {visitorsDomain}>
		<tr>
			<td>{name}</td>
			<td>{number}</td>
			<td><div style="background-color:#FFF; height:20px; width:{width}px"><br/></div></td>
			<td>{percent}%</td>
		</tr>
		</block>
		</table>
	</div>
</div>
