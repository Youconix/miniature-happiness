<section id="statsView">
	<h1>{pageTitle}</h1>

	<section class="statsItem">
		<h2>{operatingTitle}</h2>

		<p>
			<a href="javascript:adminStats.view2({month},{year})" class="button">{back}</a>
		</p>

		<table>
			<tbody>
				<tr>
					<td><label>{osTitle}</label></td>
					<td><label>{amount}</label></td>
				</tr>
				<block{OS}>
				<tr>
					<td>{name}</td>
					<td>{number}</td>
				</tr>
				</block>
			</tbody>
		</table>

		<p>
			<a href="javascript:adminStats.view2({month},{year})" class="button">{back}</a>
		</p>
	</section>
</section>