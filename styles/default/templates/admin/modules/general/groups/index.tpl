<section id="groups">
	<section class="item_header">
		<h1>{groupTitle}</h1>
	</section>

	<section class="item_body">
		<table>
			<thead>
				<tr>
					<td>{headerID}</td>
					<td>{headerName}</td>
					<td>{headerDescription}</td>
					<td>{headerAutomatic}</td>
				</tr>
			</thead>
			<tbody>
				<block{groupBlocked}>
				<tr data-id="-1" class="group_not_editable">
					<td>{id}</td>
					<td>{name}</td>
					<td>{description}</td>
					<td>{default}</td>
				</tr>
				</block>
				<block{group}>
				<tr data-id="{id}" class="group_editable">
					<td>{id}</td>
					<td>{name}</td>
					<td>{description}</td>
					<td>{default}</td>
				</tr>
				</block>
			</tbody>
		</table>

		<p>
			<input type="button" id="groupAddButton" value="{addButton}">
		</p>
	</section>
</section>