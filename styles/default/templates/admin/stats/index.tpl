<section id="statsView">
	<h1>{pageTitle}</h1>

	<section class="statsItemHits">
		<h2>{lastMonth} {HitsUniqueTitle} {nextMonth}</h2>

		<table>
			<tbody>
				<tr>
					<block{visitorsDay}>
					<td><label>{day}</label></td>
					</block>
				</tr>
				<tr>
					<block{visitorsNumber}>
					<td>{number}</td>
					</block>
				</tr>
			</tbody>
		</table>
		</div>

		<section class="statsItemHits">
			<h2>{lastMonth} {HitsTitle} {nextMonth}</h2>

			<table>
				<tbody>
					<tr>
						<block{hitsDay}>
						<td class="bold">{day}</td>
						</block>
					</tr>
					<tr>
						<block{hitsNumber}>
						<td>{number}</td>
						</block>
					</tr>
					<tr>
						<td colspan="2"><br /></td>
					</tr>
				</tbody>
			</table>

			<table>
				<tbody>
					<block{visitorsHour}>
					<tr>
						<td>{hour}</td>
						<td>{number}</td>
						<td><div
								style="background-color: #FFF; height: 20px; width: {width">
								<br />
							</div></td>
						<td>{percent}%</td>
					</tr>
					</block>
				</tbody>
			</table>
		</section>

		<section class="statsItemHits">
			<h2>{pagesTitle}</h2>

			<table>
				<tbody>
					<block{visitorsPage}>
					<tr>
						<td>{page}</td>
						<td>{number}</td>
						<td><div
								style="background-color: #FFF; height: 20px; width: {width">
								<br />
							</div></td>
						<td>{percent}%</td>
					</tr>
					</block>
				</tbody>
			</table>
		</section>

		<section class="statsItem">
			<h2>{screenSizesTitle}</h2>

			<table>
				<tbody>
					<tr>
						<td><label>{screenSize}</label></td>
						<td><label>{amount}</label></td>
					</tr>
					<block{screenSize}>
					<tr>
						<td>{name}</td>
						<td>{number}</td>
					</tr>
					</block>
				</tbody>
			</table>

			<p>
				<a href="javascript:adminStats.viewSizes({month},{year})"
					class="button">{fullList}</a>
			</p>
		</section>

		<section class="statsItem">
			<h2>{operatingTitle}</h2>

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
				<a href="javascript:adminStats.viewOS({month},{year})"
					class="button">{fullList}</a>
			</p>
		</section>

		<section class="statsItem">
			<h2>{browsersTitle}</h2>

			<table>
				<tbody>
					<tr>
						<td><label>{browserTitle}</label></td>
						<td><label>{amount}</label></td>
					</tr>
					<block{browser}>
					<tr>
						<td>{name}</td>
						<td>{number}</td>
					</tr>
					</block>
				</tbody>
			</table>

			<p>
				<a href="javascript:adminStats.viewBrowsers({month},{year})"
					class="button">{fullList}</a>
			</p>
		</section>

		<section class="statsItem">
			<h2>{screenColorsTitle}</h2>

			<table>
				<tbody>
					<tr>
						<td><label>{colorTitle}</label></td>
						<td><label>{amount}</label></td>
					</tr>
					<block{screenColor}>
					<tr>
						<td>{name}</td>
						<td>{number}</td>
					</tr>
					</block>
				</tbody>
			</table>
		</section>

		<section class="statsItem">
			<h2>{referencesTitle}</h2>

			<table>
				<tbody>
					<tr>
						<td><br /></td>
						<td><label>{amount}</label></td>
						<td><br /></td>
					</tr>
					<block{reference}>
					<tr>
						<td>{name}</td>
						<td>{number}</td>
						<td>{percent}%</td>
					</tr>
					</block>
				</tbody>
			</table>
		</section>

		<section class="statsItem">
			<h2>{domainsTitle}</h2>

			<table>
				<tbody>
					<tr>
						<td><br /></td>
						<td><label>{amount}</label></td>
						<td colspan="2"><br /></td>
					</tr>
					<block{visitorsDomain}>
					<tr>
						<td>{name}</td>
						<td>{number}</td>
						<td><div
								style="background-color: #FFF; height: 20px; width: {width">
								<br />
							</div></td>
						<td>{percent}%</td>
					</tr>
					</block>
				</tbody>
			</table>
		</section>
	</section>