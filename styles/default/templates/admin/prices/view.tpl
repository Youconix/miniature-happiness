<div id="pricesAdmin">
	<h1>{pricesText}</h1>

	<h2 class="Notice" id="notice"></h2>

	<div class="adminPanel">
		<h1>{familyText}</h1>

		<h2>{option} 1</h2>

		<table>
		<tr>
			<td class="tableText">{startupText}</td>
			<td><input type="text" id="startup1" value="{startup1}" onblur="adminPrices.check(this.id)"/></td>
		</tr>
		<tr>
			<td class="tableText">{preparationText}</td>
			<td><input type="text" id="preparation1" value="{preparation1}" onblur="adminPrices.check(this.id)"/></td>
		</tr>
		<tr>
			<td class="tableText">{firstMonthText}</td>
			<td><input type="text" id="first_month1" value="{firstMonth1}" onblur="adminPrices.check(this.id)"/></td>
		</tr>
		<tr>
			<td class="tableText">{secondMonthText}</td>
			<td><input type="text" id="second_month" value="{secondMonth1}" onblur="adminPrices.check(this.id)"/></td>
		</tr>
		<tr>
			<td class="tableText">{remainingText}</td>
			<td><input type="text" id="rest1" value="{restMonths1}" onblur="adminPrices.check(this.id)"/></td>
		</tr>
		</table>

		<h2>{option} 2</h2>

		<table>
		<tr>
			<td class="tableText">{startupText}</td>
			<td><input type="text" id="startup2" value="{startup2}" onblur="adminPrices.check(this.id)"/></td>
		</tr>
		<tr>
			<td class="tableText">{preparationText}</td>
			<td><input type="text" id="preparation2" value="{preparation2}" onblur="adminPrices.check(this.id)"/></td>
		</tr>
		<tr>
			<td class="tableText">{firstMonthText}</td>
			<td><input type="text" id="first_month2" value="{firstMonth2}" onblur="adminPrices.check(this.id)"/></td>
		</tr>		
		<tr>
			<td class="tableText">{remainingText}</td>
			<td><input type="text" id="rest2" value="{restMonths2}" onblur="adminPrices.check(this.id)"/></td>
		</tr>
		</table>

		<h1>{aupairText}</h1>

		<table>
		<tr>
			<td class="tableText">{startupText}</td>
			<td><input type="text" id="startup_aupair" value="{startupAupair}" onblur="adminPrices.check(this.id)"/></td>
		</tr>
		<tr>
			<td class="tableText">{preparationText}</td>
			<td><input type="text" id="preparation_aupair" value="{preparationAupair}" onblur="adminPrices.check(this.id)"/></td>
		</tr>
		<tr>
			<td class="tableText">{aftercareText}</td>
			<td><input type="text" id="aftercare" value="{aftercareAupair}" onblur="adminPrices.check(this.id)"/></td>
		</tr>
		</table>
		
		<h5>0 == {Free}<br/>
		0.5 == {halfPrice}</h5>
		
		<h1>{randExchangeRate}</h1>
		
		<table>
		<tr>
			<td class="bold">&euro;</td>
			<td class="bold">R</td>
		</tr>
		<tr>
			<td class="bold">1</td>
			<td><input type="text" id="rand" value="{rand}" onblur="adminPrices.checkRate(this.id)"/>
		</tr>
		</table>

		<p><a href="javascript:adminPrices.save()" class="button">{saveButton}</a></p>
	</div>
</div>
