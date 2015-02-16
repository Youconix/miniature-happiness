<section id="statsView">
	<h1>{pageTitle}</h1>

	<section class="statsItem">
		<h2>{screenSizesTitle}</h2>

		<p>
			<a href="javascript:adminStats.view2({month},{year})" class="button">{back}</a>
		</p>

		<table>
			<tbody>
				<tr>
					<td><label>{screenSize}</label></td>
					<td>{amount}</label></td>
				</tr>
				<block{screenSize}>
				<tr>
					<td>{name}</td>
					<td>{number}</td>
				</tr>
				</block>
				</section>
		</table>

		<p>
			<a href="javascript:adminStats.view2({month},{year})" class="button">{back}</a>
		</p>
	</section>
</section>