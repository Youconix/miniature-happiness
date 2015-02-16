<section id="groups">
	<section class="item_header">
		<h1>{groupTitle}</h1>

		<nav>
			<ul>
				<li id="users_back">{buttonBack}</li>
			</ul>
		</nav>
	</section>

	<section class="item_body">
		<fieldset>
			<label for="name" class="label">{headerName}</label> <input
				type="text" name="name" value="{nameDefault}" id="name" required>
		</fieldset>
		<fieldset>
			<label for="description" class="label">{headerDescription}</label>
			<textarea name="description" id="description" required>{descriptionDefault}</textarea>
		</fieldset>
		<fieldset>
			<label for="automatic" class="label">{headerAutomatic}</label> <input
				type="checkbox" name="automatic" id="default_1">
		</fieldset>
		<fieldset>
			<input type="button" id="groupSave" value="{buttonSubmit}"
				class="button"> <input type="button" id="groupCancel"
				value="{buttonCancel}" style="float: right">
		</fieldset>
	</section>
</section>