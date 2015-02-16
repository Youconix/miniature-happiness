<section id="statsView">
	<h1>{pageTitle}</h1>

	<section class="statsItem">
		<h2>{browsersTitle}</h2>

		<p>
			<a href="javascript:adminStats.view2({month},{year})" class="button">{back}</a>
		</p>

		<table>
			<tbody>
				<tr>
					<td><label>{browserTitle}</label></td>
					<td><label>{browserVersion}</label></td>
					<td><label>{amount}</label></td>
				</tr>
				<block{browser}>
				<tr>
					<td>{name}</td>
					<td>{version}</td>
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