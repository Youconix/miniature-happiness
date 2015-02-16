<section id="groups">
	<h1>{groupTitle}</h1>

	<section>
		<table>
			<thead>
				<tr>
					<td>{headerUser}</td>
					<td>{headerRights}</td>
				</tr>
			</thead>
			<tbody>
				<block{user}>
				<tr>
					<td><a href="javascript:adminUsers.viewUser({id})">{username}</a></td>
					<td>{rights}</td>
				</tr>
				</block>
			</tbody>
		</table>

		<a href="javascript:adminGroups.view()" class="button">{backButton}</a>
	</section>
</section>